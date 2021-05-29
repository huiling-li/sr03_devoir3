function removeQuotes(myinput) {
   var regCarToClean = new RegExp("[']", "g");
   myinput.value = myinput.value.replace(regCarToClean, '');
}