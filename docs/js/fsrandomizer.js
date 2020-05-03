//Alerts
function setAlert(id, message=null, type=null) {
	if(!message) { $('#'+id).hide(); }
	else {
		$("#"+id)
			.text(message)
			.attr('class', 'alert alert-' + type)
			.show();
	}
}

/* Landing Page
 * No need to run a check on whether we're on the landing page, since jQuery does that check for us
 * the code will simply not run if the elements don't exist without throwing any errors
 */
//NavBar: Input ID
$("#InputIDForm").submit(function(){
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

//Generator: Button
$("#generator").click(function(){
	//Let the user know it's loading
	setAlert('alert', 'Your list is being created...', 'info');
	
	//Generate a list with that ID
	var listGenerated = false;
	$.ajax({
		dataType: "json",
		data: { generate: null },
		async: false,
		success: function(data) {
			//What to do after getting a reply
			if(data) {
				listGenerated = true;
				setAlert('alert', ('Your list has been created: ' + data + '!'), 'success');
				window.location = './' + data;
			}
		}
	});
	
	//If this runs, then there was an unknown error
	if(listGenerated == false) { setAlert('alert', 'Uh oh... There was an unknown error creating your list.', 'danger'); }
});