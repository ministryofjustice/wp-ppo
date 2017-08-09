<?php

if (!class_exists('WP_CLI')) {
  throw new Exception('This script must be run from the CLI');
}

use \WP_CLI\Utils;

/**
 * Migrate document posts and associated attachments to use new data structure.
 */

class MigrateDocuments extends WP_CLI_Command {
  /**
   * Generate 'document-upload-attachment-id' post meta for documents.
   *
   * ## OPTIONS
   *
   * [--regenerate-all]
   * : Regenerate for all posts
   *
   * @param array $args Positional arguments
   * @param array $assoc_args Associative arguments
   */
  public function add_attachment_ids($args, $assoc_args = []) {
    $query_args = [
      'post_type' => 'document',
      'posts_per_page' => -1,
    ];

    if (!isset($assoc_args['regenerate-all'])) {
      $query_args['meta_query'] = [
        'relation' => 'OR',
        [
          'key' => 'document-upload-attachment-id',
          'compare' => 'NOT EXISTS',
        ],
        [
          'key' => 'document-upload-attachment-id',
          'value' => '',
        ],
      ];
    }

    $documents = new WP_Query($query_args);

    WP_CLI::log(WP_CLI::colorize("Generating attachment IDs for %c{$documents->post_count}%n documents"));

    $failures = [];

    $progress = Utils\make_progress_bar('', $documents->post_count);
    foreach ($documents->posts as $document) {
      save_document_upload_id($document->ID, $document);
      $success = get_post_meta($document->ID, 'document-upload-attachment-id', true);
      if (!$success) {
        array_push($failures, $document);
      }
      $progress->tick();
    }
    $progress->finish();

    if (empty($failures)) {
      WP_CLI::success('Completed for all documents');
    }
    else {
      WP_CLI::warning(WP_CLI::colorize('Completed, but with %r' . count($failures) . ' failures%n:'));
      $output = array_map(function($document) {
        $document_type_terms = wp_get_post_terms($document->ID, 'document_type');
        if (count($document_type_terms) > 0) {
          $document_type = $document_type_terms[0]->name;
        }
        else {
          $document_type = '';
        }

        return [
          'ID' => $document->ID,
          'Post Title' => $document->post_title,
          'Document Type' => $document_type,
          'Document URL' => get_post_meta($document->ID, 'document-upload', true),
        ];
      }, $failures);
      Utils\format_items('table', $output, array_keys($output[0]));
    }
  }

  /**
   * Remove document post thumbnails and associated media library items.
   */
  public function remove_thumbnails() {
    WP_CLI::log(get_post_meta(3961, '_thumbnail_id', true));
    exit;

    $documents = new WP_Query([
      'post_type' => 'document',
      'posts_per_page' => -1,
      'meta_query' => [
        [
          'key' => '_thumbnail_id',
          'compare' => 'EXISTS',
        ]
      ],
    ]);

    var_dump($documents);
    exit;

    WP_CLI::log(WP_CLI::colorize("Removing old post thumbnails for %c{$documents->post_count}%n documents"));

    $progress = Utils\make_progress_bar('', $documents->post_count);
    foreach ($documents->posts as $document) {
      array_push($failures, $document);
      $progress->tick();
    }
    $progress->finish();

    if (empty($failures)) {
      WP_CLI::success('Completed for all documents');
    }
    else {
      WP_CLI::warning(WP_CLI::colorize('Completed, but with %r' . count($failures) . ' failures%n:'));
      $output = array_map(function($document) {
        return [
          'ID' => $document->ID,
          'Post Title' => $document->post_title,
          'Thumbnail ID' => get_post_meta($document->ID, '_thumbnail_id', true),
        ];
      }, $failures);
      Utils\format_items('table', $output, array_keys($output[0]));
    }
  }

  /**
   * Import attachments that reference old 'ReddotImportContent' URLs.
   */
  public function import_reddot_attachments() {
    $documents = new WP_Query([
      'post_type' => 'document',
      'posts_per_page' => -1,
      'meta_query' => [
        [
          'key' => 'document-upload',
          'value' => '/ReddotImportContent/',
          'compare' => 'LIKE',
        ],
      ],
    ]);

    if ($documents->post_count === 0) {
      WP_CLI::log("No RedDot attachments were found. Hooray!");
      return;
    }

    WP_CLI::log(WP_CLI::colorize("Importing RedDot attachments for %c{$documents->post_count}%n documents"));

    $failures = [];

    $progress = Utils\make_progress_bar('', $documents->post_count);
    foreach ($documents->posts as $document) {
      $document_path = trim(get_post_meta($document->ID, 'document-upload', true));
      $download_url = 'http://www.ppo.gov.uk' . $document_path;

      try {
        $image = $this->import_attachment_from_url($download_url, $document->ID);
      }
      catch (Exception $e) {
        array_push($failures, [
          'document' => $document,
          'error' => $e->getMessage(),
          'attempted_url' => $download_url,
        ]);
        continue;
      }

      update_post_meta($document->ID, 'document-upload-attachment-id', $image);
      update_post_meta($document->ID, 'document-upload', wp_get_attachment_url($image));

      $progress->tick();
    }
    $progress->finish();

    if (empty($failures)) {
      WP_CLI::success('Completed for all documents');
    }
    else {
      WP_CLI::warning(WP_CLI::colorize('Completed, but with %r' . count($failures) . ' failures%n:'));
      $output = array_map(function($failure) {
        return [
          'ID' => $failure['document']->ID,
          'Post Title' => $failure['document']->post_title,
          'Attempted Download URL' => $failure['attempted_url'],
          'Error' => $failure['error'],
        ];
      }, $failures);
      Utils\format_items('table', $output, array_keys($output[0]));
    }
  }

  /**
   * Import an attachment into the WordPress media library given its URL.
   *
   * @param string $url URL of the file to import
   * @param int $post_id ID of the post this attachment is associated with
   * @return int ID of the imported attachment
   * @throws Exception
   */
  protected function import_attachment_from_url($url, $post_id) {
    $tmp = download_url($url);
    if (is_wp_error($tmp)) {
      throw new Exception($tmp->get_error_message());
    }

    $file = [
      'name' => basename($url),
      'tmp_name' => $tmp,
    ];

    $id = media_handle_sideload($file, $post_id);

    if (is_wp_error($id)) {
      @unlink($tmp);
      throw new Exception($id->get_error_message());
    }

    return $id;
  }
}

WP_CLI::add_command('migrate-documents', 'MigrateDocuments');

/*$documents = (new WP_Query([
  'post_type' => 'document',
  'posts_per_page' => -1,
  'meta_query' => [
    'relation' => 'OR',
    [
      'key' => 'document-upload-attachment-id',
      'compare' => 'NOT EXISTS',
    ],
    [
      'key' => 'document-upload-attachment-id',
      'value' => '',
    ]
  ],
]))->posts;

WP_CLI::line('Found ' . count($documents) . ' posts:');

foreach ($documents as $document) {
  WP_CLI::line(get_post_meta($document->ID, 'document-upload', true));
}

exit;*/

/**
 * 1. Populate 'document-upload-attachment-id' post meta for all documents
 */
/*$documents = new WP_Query([
  'post_type' => 'document',
  'posts_per_page' => -1,
  'meta_query' => [
    'relation' => 'OR',
    [
      'key' => 'document-upload-attachment-id',
      'compare' => 'NOT EXISTS',
    ],
    [
      'key' => 'document-upload-attachment-id',
      'value' => '',
    ],
  ],
]);

$progress = \WP_CLI\Utils\make_progress_bar("Populate 'attachment-id' for {$documents->post_count} documents", $documents->post_count);
foreach ($documents->posts as $document) {
  save_document_upload_id($document->ID, $document);
  $progress->tick();
}
$progress->finish();

exit;*/

/**
 * 2. Remove document post thumbnails, and delete associated attachments.
 */
/*$documents = (new WP_Query([
  'post_type' => 'document',
  'posts_per_page' => -1,
  'meta_query' => [
    [
      'key' => '_thumbnail_id',
      'compare' => 'EXISTS',
    ]
  ],
]))->posts;

WP_CLI::line('2. Removing post thumbnails');

foreach ($documents as $document) {
  WP_CLI::line($document->post_title);
  $attachment_id = get_post_meta($document->ID, '_thumbnail_id');
  foreach ($attachment_id as $id) {
    wp_delete_attachment($id, true);
  }
  delete_post_meta($document->ID, '_thumbnail_id');
}

WP_CLI::line('Done');
*/