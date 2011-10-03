
function validateform(){
          // return false;
           jQuery(document).ready(function(){
      
            });
           if(document.getElementById("f_name").value==""){
               document.getElementById("err_fname").innerHTML="First name can not be blanck";
               return false;
           }
           if(document.getElementById("l_name").value==""){
               document.getElementById("err_lname").innerHTML="Last name can not be blanck";
               return false;
           }
           if(document.getElementById("c_name").value==""){
               document.getElementById("err_cname").innerHTML="Company name can not be blanck";
               return false;
           }
           if(document.getElementById("email").value==""){
               document.getElementById("err_email").innerHTML="Email can not be blanck";              
               return false;
           }
           else if(checkEmail()==false){
               document.getElementById("err_email").innerHTML="Somthing wrong with your email";
               jQuery.post("rpc/singup.php",{email:"email"},function(data){
                   if(!data){                      
                     document.getElementById("err_email").innerHTML="Email used";
                     return false;
                   }
               });
               return false;
           }
           
           if(document.getElementById("pass").value==""){
               document.getElementById("err_pass").innerHTML="Password is empty";
               return false;
           }  
            
           
  
       } 
       
function loadImage(){
    $(function () {
  var img = new Image();
  
  // wrap our new image in jQuery, then:
  $(img)
    // once the image has loaded, execute this code
    .load(function () {
      // set the image hidden by default    
      $(this).hide();
    
      // with the holding div #loader, apply:
      $('#loader')
        // remove the loading class (so no background spinner), 
        .removeClass('loading')
        // then insert our image
        .append(this);
    
      // fade our image in to create a nice effect
      $(this).fadeIn();
    })
    
    // if there was an error loading the image, react accordingly
    .error(function () {
      // notify the user that the image could not be loaded
    })
    
    // *finally*, set the src attribute of the new image to our image
    .attr('src', 'upload_pic/apartment.jpg');
});
}       
function checkEmail() {
    var email = document.getElementById('email');
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(email.value)){           
        return false;
    }
}