var taskModel = {
    taskData: ""
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
            taskToUpdate.completed = checkbox.checked;
            checkmate.updateTask(usegm, usenotation, taskToUpdate, this.handleServerResponse);
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

        var taskRow = document.getElementById("taskRow" + taskId);
        taskRow.parentNode.removeChild(taskRow);

        if (xhr) {
            console.log("Deleting task " + taskId);
            taskToUpdate = this.findTaskDataFromId (taskId)
            if (taskToUpdate) {
                taskToUpdate.sortPosition = -1;
                checkmate.updateTask(usegm, usenotation, taskToUpdate, this.handleServerResponse);
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

taskModel.findTaskDataFromId = function (taskId) {
    console.log("finding " + taskId + " in " + JSON.stringify(this.taskData));
    var taskToUpdate;
    for (var i=0;i<this.taskData.length; i++) {
        if (this.taskData[i].guid == taskId) {
            taskToUpdate = this.taskData[i];
        }
    }
    return taskToUpdate;
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