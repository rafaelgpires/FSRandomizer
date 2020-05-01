//NavBar: Input ID
$("#InputIDForm").submit(function(event){
	//Check validity of the ID
	var continueSubmit = true;
	$.ajax({
		dataType: "json",
		data: {UniqueID: $("#InputID").val(), output: "validate"},
		async: false,
		success: function(data) {
			if(data) $("#InputID").removeClass("is-invalid").addClass("is-valid");
			else { 
				$("#InputID").removeClass("is-valid").addClass("is-invalid");
				continueSubmit = false;
			}
		}
	});
	
	//Either block the submit, or let it through
	return continueSubmit;
});