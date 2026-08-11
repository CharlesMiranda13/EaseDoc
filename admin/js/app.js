/**
 * app.js — Admin Portal Settings & Utility Scripts
 */
$(document).ready(function() {

    $("#frmUpdateInfo").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner").show();
        var formData = new FormData(this); 
        formData.append("requestType", 'UpdateAdminInfo');  

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', 
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Admin info updated successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);  
                } else {
                    alertify.error(response.message || "Failed to update admin information.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred. Please try again.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner").hide();
            }
        });
    });

    $("#frmUpdatePassword").on("submit", function(e) {
        e.preventDefault(); 
    
        var new_password = $("#new_password").val();
        var confirm_password = $("#confirm_password").val();
        
        if (new_password !== confirm_password) {
            alertify.error("Passwords do not match.");
            return;
        }
    
        $("#loadingSpinner").show();
        var formData = new FormData(this); 
        formData.append("requestType", 'UpdatePassword');  
    
        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', 
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Password updated successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);  
                } else {
                    alertify.error(response.message || "Failed to update password.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred. Please try again.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner").hide();
            }
        });
    });

});