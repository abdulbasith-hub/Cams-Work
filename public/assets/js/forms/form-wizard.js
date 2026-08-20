//Basic Example
$("#example-basic").steps({
  headerTag: "h3",
  bodyTag: "section",
  transitionEffect: "slideLeft",
  autoFocus: true,
});
$("#example-finalise").steps({
  headerTag: "h3",
  bodyTag: "section",
  transitionEffect: "slideLeft",
  autoFocus: true,
});
// Basic Example with form
var form = $("#example-form");
form.validate({
  errorPlacement: function errorPlacement(error, element) {
    element.before(error);
  },
  rules: {
    confirm: {
      equalTo: "#password",
    },
  },
});
form.children("div").steps({
  headerTag: "h3",
  bodyTag: "section",
  transitionEffect: "slideLeft",
  onStepChanging: function (event, currentIndex, newIndex) {
    form.validate().settings.ignore = ":disabled,:hidden";
    return form.valid();
  },
  onFinishing: function (event, currentIndex) {
    form.validate().settings.ignore = ":disabled";
    return form.valid();
  },
  onFinished: function (event, currentIndex) {
    alert("Submitted!");
  },
});

// Advance Example

// var advanced_form = $("#example-advanced-form").show();

// advanced_form
//   .steps({
//     headerTag: "h3",
//     bodyTag: "fieldset",
//     transitionEffect: "slideLeft",
//     onStepChanging: function (event, currentIndex, newIndex) {
//       // Allways allow previous action even if the current form is not valid!
//       if (currentIndex > newIndex) {
//         return true;
//       }
//       // Forbid next action on "Warning" step if the user is to young
//       if (newIndex === 3 && Number($("#age-2").val()) < 18) {
//         return false;
//       }
//       // Needed in some cases if the user went back (clean up)
//       if (currentIndex < newIndex) {
//         // To remove error styles
//         advanced_form.find(".body:eq(" + newIndex + ") label.error").remove();
//         advanced_form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
//       }
//       advanced_form.validate().settings.ignore = ":disabled,:hidden";
//       return advanced_form.valid();
//     },
//     onStepChanged: function (event, currentIndex, priorIndex) {
//       // Used to skip the "Warning" step if the user is old enough.
//       if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
//         advanced_form.steps("next");
//       }
//       // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
//       if (currentIndex === 2 && priorIndex === 3) {
//         advanced_form.steps("previous");
//       }
//     },
//     onFinishing: function (event, currentIndex) {
//       advanced_form.validate().settings.ignore = ":disabled";
//       return advanced_form.valid();
//     },
//     onFinished: function (event, currentIndex) {
//       alert("Submitted!");
//     },
//   })
//   .validate({
//     errorPlacement: function errorPlacement(error, element) {
//       element.before(error);
//     },
//     rules: {
//       confirm: {
//         equalTo: "#password-2",
//       },
//     },
//   });


var advanced_form = $("#example-advanced-form").show();

advanced_form.steps({
  headerTag: "h3",
  bodyTag: "fieldset",
  transitionEffect: "slideLeft",
  onStepChanging: function (event, currentIndex, newIndex) {
    // Allow previous action, always
    if (currentIndex > newIndex) {
      return true;
    }

    // You can add additional conditions for validation here
    if (currentIndex < newIndex) {
      advanced_form.find(".body:eq(" + newIndex + ") label.error").remove();
      advanced_form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
    }

    advanced_form.validate().settings.ignore = ":disabled,:hidden";
    return advanced_form.valid();
  },
  onStepChanged: function (event, currentIndex, priorIndex) {
    // Custom logic to skip certain steps if needed
    if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
      advanced_form.steps("next");
    }
  },
  onFinishing: function (event, currentIndex) {
    advanced_form.validate().settings.ignore = ":disabled";
    return advanced_form.valid();
  },
  onFinished: function (event, currentIndex) {
    alert("Form Submitted!");
  },
}).validate({
  errorPlacement: function errorPlacement(error, element) {
    element.before(error);
  },
  rules: {
    confirm: {
      equalTo: "#password-2",
    },
  },
});


$('#next-button').click(function () {
  advanced_form.steps('next');
});

$('#previous-button').click(function () {
  advanced_form.steps('previous');
});




 


// Dynamic Manipulation
$("#example-manipulation").steps({
  headerTag: "h3",
  bodyTag: "section",
  enableAllSteps: true,
  enablePagination: false,
});

//Vertical Steps

$("#example-vertical").steps({
  headerTag: "h3",
  bodyTag: "section",
  transitionEffect: "slideLeft",
  stepsOrientation: "vertical",
});

//Custom design form example
$(".tab-wizard").steps({
  headerTag: "h6",
  bodyTag: "section",
  transitionEffect: "fade",
  titleTemplate: '<span class="step">#index#</span> #title#',
  labels: {
    finish: "Submit",
  },
  onFinished: function (event, currentIndex) {
    swal(
      "Form Submitted!",
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat eleifend ex semper, lobortis purus sed."
    );
  },
});

