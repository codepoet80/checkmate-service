var checkmate = {
    //globals
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