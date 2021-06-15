var checkmate = {
    //globals
    actionUrl: ""
}

checkmate.buildURL = function(actionType) {
    var useUrl = this.actionUrl.split("tasks.php");
    useUrl = useUrl[0];
    useUrl = useUrl + actionType + ".php";
    return useUrl;
}

checkmate.detectXHR = function() {
    if (typeof new XMLHttpRequest().responseType === 'string') {
        try {
            var xhr = new XMLHttpRequest();
            return true;
        } catch (ex) {
            return false;
        }
    }
    return false;
}

checkmate.updateTask = function(grandmaster, notation, taskData, callback) {
    var theQuery = this.buildURL("update-notation") + "?move=" + notation;
    console.log ("Update task list at URL: " + theQuery + " with data: " + JSON.stringify(taskData));
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", theQuery);
    xmlhttp.setRequestHeader("grandmaster", atob(grandmaster));
    xmlhttp.send(JSON.stringify(taskData));
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            console.log ("Got Update response from service: " + xmlhttp.responseText);
            if (callback)
                callback(xmlhttp.responseText);
        }
    };
}