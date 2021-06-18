/* Drag and Drop */
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
    console.log("dropped " + data + " on " + event.target.id);
    //original row
    var rowId = data.replace("drag", "taskRow");
    var row = document.getElementById(rowId);
    row.classList.remove("dragging");
    //target row
    rowId = event.target.id.replace("drag", "taskRow");
    row = document.getElementById(rowId);
    row.classList.remove("active");
}
/* End Drag and Drop */