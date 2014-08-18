/**
 * Created by guillaumedeneux on 09/06/2014.
 */
var minWidthBar = 600;
var bodyElem = $$('body')[0];
var sizeBody = bodyElem.getWidth();

document.observe("dom:loaded", function() {
    resizeWindow();
});

Event.observe(window, "resize", function() { resizeWindow()});

function resizeWindow(){
    var sizeWindow = window.innerWidth;
    var diffsize = Math.max(sizeWindow - sizeBody, minWidthBar);


    bodyElem.setStyle({
        float: 'left',
        width: sizeWindow - diffsize+'px'
    });
    $$('.panelContent').each(
        function (Element) {
            Element.setStyle({
                width: diffsize+'px'
            });
        });
}