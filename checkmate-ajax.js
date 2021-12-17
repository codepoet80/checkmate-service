var checkmate = {
    actionUrl: "",    
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

checkmate.clearCompletedTasks = function(grandmaster, notation, callback) {
    var theQuery = this.buildURL("cleanup-notation") + "?move=" + notation;
    console.log ("Clear completed tasks at URL: " + theQuery);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", theQuery);
    xmlhttp.setRequestHeader("grandmaster", atob(grandmaster));
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            console.log ("Got Clear completed tasks response from service: " + xmlhttp.responseText);
            if (callback)
                callback(xmlhttp.responseText);
        }
    };
}

checkmate.getTasks = function(grandmaster, notation, callback) {
    var theQuery = this.buildURL("read-notation") + "?move=" + notation;
    console.log ("Get tasks at URL: " + theQuery);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", theQuery);
    xmlhttp.setRequestHeader("grandmaster", atob(grandmaster));
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            console.log ("Got Get tasks response from service: " + xmlhttp.responseText);
            if (callback)
                callback(xmlhttp.responseText);
        }
    };
}

checkmate.redrawTaskTable = function(taskData) {
    var theQuery = this.buildURL("web-tasktable");
    console.log("redraw task table with " + theQuery + " and data " + JSON.stringify(taskData));
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", theQuery);
    xmlhttp.send(JSON.stringify(taskData));
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            document.getElementById("tableTasks").innerHTML = xmlhttp.responseText;
            swapTech();
        }
    };
}