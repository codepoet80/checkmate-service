function dragStart(event) {
    console.log("dragging " + event.target.id);
    var rowId = event.target.id.replace("drag", "taskRow");
    var row = document.getElementById(rowId);
    row.classList.add("dragging");
    event.dataTransfer.setData("Text", event.target.id);
}

function dragEnter(event) {
    console.log("entering " + event.target.id);
    var rowId = event.target.id.replace("drag", "taskRow");
    var row = document.getElementById(rowId);
    row.classList.add("active");
}

function dragLeave(event) {
    console.log("exiting " + event.target.id);
    var rowId = event.target.id.replace("drag", "taskRow");
    var row = document.getElementById(rowId);
    row.classList.remove("active");
}

function allowDrop(event) {
    event.preventDefault();
}

function drop(event) {
    event.preventDefault();
    var data = event.dataTransfer.getData("Text");
    //alert("dropped " + data + " on " + event.target.id);
    //original row
    rowId = data.replace("drag", "taskRow");
    row = document.getElementById(rowId);
    row.classList.remove("dragging");
    fromId = rowId.replace("taskRow", "");
    fromIndex = taskModel.findTaskIndexFromId(fromId);
    //target row
    rowId = event.target.id.replace("drag", "taskRow");
    row = document.getElementById(rowId);
    row.classList.remove("active");
    toId = rowId.replace("taskRow", "");
    var toIndex = taskModel.findTaskIndexFromId(toId);
    
    //re-sort list
    var items = taskModel.taskData;
    var newPos = 1;
    for (var i = items.length - 1; i >= 0; i--) {
        newPos++;
    }

    console.log ("move " + fromId + " from " + fromIndex + " to " + toId + " " + toIndex);
    items.move(fromIndex, toIndex);

    newPos = 1;
    for (var i = items.length - 1; i >= 0; i--) {
        items[i].sortPosition = newPos;
        newPos++;
    }

    checkmate.updateTask(taskModel.grandmaster, taskModel.notation, items, function(response){
        if (JSON.parse(response)) {
            response = JSON.parse(response);
            this.taskData = response;
            checkmate.redrawTaskTable(response.tasks);
        }
    });
}

Array.prototype.move = function(from, to) {
    this.splice(to, 0, this.splice(from, 1)[0]);
};