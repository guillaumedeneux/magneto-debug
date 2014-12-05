/**
 * Created by guillaumedeneux on 09/06/2014.
 */
var minWidthBar = 500;
var bodyElem = $$('body')[0];
var sizeBody = bodyElem.getWidth();


document.observe("dom:loaded", function() {
    $$('#djDebugPanelList li a').invoke('observe', 'click', displayPanel);
});


function displayPanel(event){
    var current = $(Event.element(event));
    var currentPanel = $(current.readAttribute('class'));

    if(currentPanel.visible()){
        currentPanel.hide();
        current.getOffsetParent().removeClassName('active');
    }else{
        $$('.panelContent').each(function(element) {element.hide()});
        $$('#djDebugToolbar li').each(function(element) {element.removeClassName('active')});

        currentPanel.show();
        current.getOffsetParent().addClassName('active');
    }

    return false;
}