/**
 * Created by guillaumedeneux on 09/06/2014.
 */
$$('body')[0].setStyle({
    float: 'left'
});

document.observe("dom:loaded", function() {
    resizeWindow();
});

Event.observe(window, "resize", function() { resizeWindow()});

function resizeWindow(){
    var sizeBody = $$('body')[0].getWidth();
    var sizeWindow = window.innerWidth;
    console.log(sizeWindow);
    var diffsize = sizeWindow - sizeBody;
    $$('.panelContent').each(
        function (Element) {
            Element.setStyle({
                width: diffsize+'px'
            });
        });
}