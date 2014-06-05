function setConformingHeight(el, newHeight) {
    // set the height to something new, but remember the original height in case things change
    el.data("originalHeight", (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight")));
    el.height(newHeight);
//    console.log(el);
//    console.log(el.height());
}

function getOriginalHeight(el) {
    // if the height has changed, send the originalHeight
    return (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight"));
}

function columnConform(selector) {

    var currentTallest = 0;
    var currentRowStart = 0;
    var rowDivs = new Array();

    // find the tallest DIV in the row, and set the heights of all of the DIVs to match it.
    $(selector).each(function(index) {

        if (currentRowStart != $(this).position().top) {

            // we just came to a new row.  Set all the heights on the completed row
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++)
                setConformingHeight(rowDivs[currentDiv], currentTallest);

            // set the variables for the new row
            rowDivs.length = 0; // empty the array
            currentRowStart = $(this).position().top;
            currentTallest = getOriginalHeight($(this));
            rowDivs.push($(this));

        } else {

            // another div on the current row.  Add it to the list and check if it's taller
            rowDivs.push($(this));
            currentTallest = (currentTallest < getOriginalHeight($(this))) ? (getOriginalHeight($(this))) : (currentTallest);

        }
        // do the last row
        for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++)
            setConformingHeight(rowDivs[currentDiv], currentTallest);

    });

}

$(window).load(function() {
    columnConform('nav>ul>li>a');
    columnConform('#home-cta-container .cta-inner');
    columnConform('#home-cta-container .cta-inner h2');

    $(window).resize(function() {
//        console.log("resize");
        columnConform('nav>ul>li>a');
        columnConform('#home-cta-container .cta-inner');
        columnConform('#home-cta-container .cta-inner h2');
    });

});