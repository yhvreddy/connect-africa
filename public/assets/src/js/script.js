function isNumberKey(evt) {
    var charCode = evt.which ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
    return true;
}

function restrictToFloat(event) {
    // Get the input value
    var inputValue = event.target.value;

    // Get the key that was pressed
    var key = event.key;

    // Allow only numeric characters (0-9), decimal point (.), and backspace
    if (key === "." && inputValue.indexOf(".") !== -1) {
        // Allow only one decimal point
        event.preventDefault();
    } else if (key !== "." && key !== "Backspace" && isNaN(parseInt(key))) {
        // Prevent input of non-numeric characters (excluding decimal point and backspace)
        event.preventDefault();
    }
}
