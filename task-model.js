var taskModel = {
    taskData: "",
    grandmaster: "",
    notation: ""
}

taskModel.upgradeUX = function() {
    document.getElementById("divCancel").innerHTML = "<img src=\"images/refresh.png\" class=\"controlButton\" onclick=\"taskModel.doRefresh()\"/>";
    document.getElementById("divCleanup").innerHTML = "<img src=\"images/sweep.png\" class=\"controlButton\" onclick=\"taskModel.doCleanup()\"/>";
    document.getElementById("divSave").innerHTML = "<img src=\"images/save.png\" class=\"controlButton\" onclick=\"doSave()\"/>";
    var draggers = document.getElementsByClassName("dragHandle");
    for (var i = 0; i < draggers.length; i++) {
        draggers[i].src = "images/handle.gif";
    }
    var links = document.getElementsByClassName("editLink");
    for (var i = 0; i < links.length; i++) {
        links[i].style.display = "none";
    }
    var imageWraps = document.getElementsByClassName("editImageWrapper");
    for (var i = 0; i < imageWraps.length; i++) {
        imageWraps[i].style.display = "block";
        imageWraps[i].style.textAlign = "right";
    }
    var images = document.getElementsByClassName("editImage");
    for (var i = 0; i < images.length; i++) {
        images[i].src = "images/pencil.gif";
    }
    links = document.getElementsByClassName("deleteLink");
    for (var i = 0; i < links.length; i++) {
        links[i].style.display = "none";
    }
    var imageWraps = document.getElementsByClassName("deleteImageWrapper");
    for (var i = 0; i < imageWraps.length; i++) {
        imageWraps[i].style.display = "block";
        imageWraps[i].style.textAlign = "right";
    }
    images = document.getElementsByClassName("deleteImage");
    for (var i = 0; i < images.length; i++) {
        images[i].src = "images/delete.gif";
    }
}

taskModel.doCheckTask = function(checkbox) {
    //Immediate feedback
    if (checkbox.checked) {
        var audio = new Audio('sounds/completed1.mp3');
    } else {
        var audio = new Audio('sounds/flick1.mp3');
    }
    audio.play();

    if (xhr) {
        var taskId = checkbox.id;
        console.log("Updating task " + taskId);
        taskToUpdate = this.findTaskDataFromId(taskId)
        if (taskToUpdate) {
            taskToUpdate.completed = Boolean(checkbox.checked);
            checkmate.updateTask(this.grandmaster, this.notation, taskToUpdate, this.handleServerResponse);
        } else {
            alert ("Error: Could not find task data to update!");
        }
    } else {
        setTimeout(() => {
            document.getElementById('formTasks').submit();
        }, 1000);
    }
}

taskModel.doTaskEdit = function(taskId) {
    //TODO: Detect AJAX and change
    var newURL = actionUrl + "&edit=" + taskId + "#editfield";
    document.location = newURL;
}

taskModel.doTaskDelete = function(taskId) {
    if (window.confirm("Are you sure you want to delete this task?")) {
        //Immediate feedback
        var audio = new Audio('sounds/delete1.mp3');
        audio.play();
        if (xhr) {
            console.log("Deleting task " + taskId);

            //remove affected row
            var taskRow = document.getElementById("taskRow" + taskId);
            taskRow.parentNode.removeChild(taskRow);

            taskToUpdate = this.findTaskDataFromId (taskId)
            if (taskToUpdate) {
                taskToUpdate.sortPosition = -1;
                checkmate.updateTask(this.grandmaster, this.notation, taskToUpdate, this.handleServerResponse);
            } else {
                alert ("Error: Could not find task data to update!");
            }
        } else {
            setTimeout(() => {
                var newURL = actionUrl + "&delete=" + taskId + "#editfield";
                document.location = newURL;
            }, 1000);
        }
    }
}

taskModel.findTaskDataFromId = function(taskId) {
    console.log("finding " + taskId + " in " + JSON.stringify(this.taskData));
    var taskToUpdate;
    for (var i=0;i<this.taskData.length; i++) {
        if (this.taskData[i].guid == taskId) {
            taskToUpdate = this.taskData[i];
        }
    }
    return taskToUpdate;
}

taskModel.doCleanup = function() {
    if (window.confirm("Are you sure you want to remove all completed tasks?")) {
        //Immediate feedback
        var audio = new Audio('sounds/trash1.mp3');
        audio.play();

        if (xhr) {
            checkmate.clearCompletedTasks(this.grandmaster, this.notation, function(response){
                if (JSON.parse(response)) {
                    response = JSON.parse(response);
                    this.taskData = response;
                    checkmate.redrawTaskTable(response.tasks);
                }
            });
        } else {
            setTimeout(() => {
                var newURL = actionUrl + "&cleanup=complete";
            document.location = newURL;
            }, 1000);
        }
    }
}

taskModel.doRefresh = function() {
    checkmate.getTasks(this.grandmaster, this.notation, function(response){
        if (JSON.parse(response)) {
            response = JSON.parse(response);
            this.taskData = response;
            checkmate.redrawTaskTable(response.tasks);
        }
    });
}

taskModel.handleServerResponse = function (response) {
    if (!response) {
        alert ("Error: No response from server!");
    } else {
        if (JSON.parse(response)) {
            response = JSON.parse(response);
            if (response.error) {
                alert (response.error)
            } else {
                if (response.tasks)
                    taskModel.taskData = response.tasks;
                else
                    alert ("Error: Server response included no tasks!");
            }
        } else {
            alert ("Error: Could not parse server response!");
        }
    }
}