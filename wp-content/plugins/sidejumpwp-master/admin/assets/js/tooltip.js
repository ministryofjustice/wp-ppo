jQuery(function($) {
var timer;
function mouseoverActiontooltip(event)
{
$("body").append("<p id='tooltip'>"+ this.rel + "</p>");
$("#tooltip").css("left",(event.pageX + 20) + "px");
$("#tooltip").css("top",(event.pageY - 10) + "px");
}

function mouseoutActiontooltip(event)
{
$("#tooltip").remove();
}

function mousemoveActiontooltip(event)
{
$("#tooltip").css("left",(event.pageX + 20) + "px");
$("#tooltip").css("top",(event.pageY - 10) + "px");
}

function mouseoverActiontooltipImage(event)
{
$("body").append("<p id='tooltip'><img src="+ this.rel + "></img></p>");
$("#tooltip").css("left",(event.pageX + 20) + "px");
$("#tooltip").css("top",(event.pageY - 10) + "px");
}

function mouseoutActiontooltipImage(event)
{
$("#tooltip").remove();
}

function mousemoveActiontooltipImage(event)
{
$("#tooltip").css("left",(event.pageX + 20) + "px");
$("#tooltip").css("top",(event.pageY - 10) + "px");
}

$('.tooltip').bind('mouseover', mouseoverActiontooltip);

$('.tooltip').bind('mouseout', mouseoutActiontooltip);

$('.tooltip').bind('mousemove', mousemoveActiontooltip);

$('.tooltipImage').bind('mouseover', mouseoverActiontooltipImage);

$('.tooltipImage').bind('mouseout', mouseoutActiontooltipImage);

$('.tooltipImage').bind('mousemove', mousemoveActiontooltipImage);

}); 