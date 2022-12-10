function notifyModern() {
    var request = new XMLHttpRequest();
    if (request) {
        modernURL = window.location.href.split("?")[0].replace("index.php", "");
        request.open('GET', modernURL + "/app", false);
        request.send();
        if (request.status == 200) {
            try {
                var prefix = (Array.prototype.slice
                    .call(window.getComputedStyle(document.documentElement, ""))
                    .join("")
                    .match(/-(moz|webkit|ms)-/))[1];
                if (["moz", "webkit"].indexOf(prefix) != -1) {
                    modernURL = window.location.href.split("?")[0].replace("index.php", "");
                    modernURL = (modernURL + "/app").replace("//app", "/app").replace("http://", "https://");
                    var myNotification = window.createNotification({});
                    myNotification({
                        title: 'Hello Modern Browser!',
                        displayCloseButton: true,
                        theme: 'info',
                        message: 'You\'re viewing the retro-friendly landing. Did you know there\'s a modern web app you can use, after you sign up?\r\n\nJust go to ' + modernURL
                    });
                }
            } catch (e) {
                //oh well
            }
        }
    }
}