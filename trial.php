<? php
$("#refresh").click(function (e) {
        e.preventDefault();
     // your array here
        var $formdata = $("#bal").serializeArray();
     // push your own data here
        $formdata.get({            
            name: $balance,
            value: document.getElementById('balance').value
        },
        content = $("#bal").html();
        // call the action function here      
       // change /mpesa/payment to your route
        $.ajax({
            url: "mpesabal.php",
            data: $formdata,
            type: "POST",
            cache: false,
            dataType: "json",
            beforeSend: function(xhr, settings) {
                // Show validating here
                $("#refresh").prop("disabled",true); // <<< disable the button
                
                // Add the spinner icon to the button, when validating 
                $("#refresh").html('<div class="spinner-border text-danger" role="status"></div> Validating');
            },
            // get feedback from server here
            success: function(data, textStatus, jqXHR){
                // Here we go >>> Processing data.status from php json
                if (data.status ==="ok"){
                    $("#refresh").html('<div class="spinner-border text-primary" role="status"></div>Processing').prop("disabled",true);
                    
                    // This checks the completion of the Transaction
                    // details is also sent from php 
                    // get the CheckoutRequestID from the array i.e details

                    // using that id, check the status
                    check_complete(data.details.CheckoutRequestID,content)
                }
                // Here we check Errors
                if (data.status ==="error"){
                    console.log(data);
                    $("#refresh").prop("disabled",false).html(content);
                    // added a div-ed class called errorHolder
                    $(".errorHolder").html('<div class="alert alert-danger">'+data.details+'</div>');
                }
                setTimeout(function() {
                    $(".errorHolder").html('');
                },25000);

            },
            error: function (jqXHR, textStatus, errorThrown){
                $("#refresh").prop("disabled",false).html(content);
                console.log('Error ' + jqXHR + ''+errorThrown);
            }
        });
    });
?>
