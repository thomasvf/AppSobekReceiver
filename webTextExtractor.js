/**
 * Created by thoma on 26/01/2017.
 */
var webPage = require('webpage');
var args = require('system').args;

var page = webPage.create();

var address = args[1];
page.open(address, function (status) {
    if(status != "success"){
        console.log("fail");
    } else {
    	console.log("__CONTENT__START__");
        console.log(page.plainText);
        console.log("__CONTENT__END__");
    }

    phantom.exit();
});