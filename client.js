//Replace for publishable key from settings page variable
var stripe_pk = document.getElementById('rwvidyas_stripe_pk').value;
var stripe = Stripe(stripe_pk);
var elements = stripe.elements();

// Set up Stripe.js and Elements to use in checkout form
var style = {
    base: {
      color: "#32325d",
      fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
      fontSmoothing: "antialiased",
      fontSize: "16px",
      "::placeholder": {
        color: "#aab7c4"
      }
    },
    invalid: {
      color: "#fa755a",
      iconColor: "#fa755a"
    }
  };

var card = elements.create('card', { style: style });
card.mount('#card-element');

card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
    displayError.textContent = event.error.message;
    } else {
    displayError.textContent = '';
    }
});
var form = document.getElementById('payment-form');
var clientSecret = document.getElementById("client_secret").value;

form.addEventListener('submit', function(ev) {
	changeLoadingState(true);
    ev.preventDefault();
    stripe.confirmCardPayment(clientSecret, {
    payment_method: {
        card: card,
        billing_details: {
        }
    }
    }).then(function(result) {
    if (result.error) {
        // Show error to your customer (e.g., insufficient funds)
        orderFailed(result.error.message);
        console.log(result.error.message);
		changeLoadingState(false);
    } else {
        // The payment has been processed!
        if (result.paymentIntent.status === 'succeeded') {
		      orderComplete(clientSecret);
          console.log('Payment succeeded!');
          setTimeout(function () { location.reload(true); }, 5000);

        changeLoadingState(false);
        }
    }
    });
});

/* Shows a success / error message when the payment is complete */
var orderComplete = function(clientSecret) {
  // Just for the purpose of the sample, show the PaymentIntent response object
  stripe.retrievePaymentIntent(clientSecret).then(function(result) {
    var paymentIntent = result.paymentIntent;
    var paymentIntentJson = JSON.stringify(paymentIntent, null, 2);

    document.querySelector(".sr-payment-form").classList.add("hidden");
    document.querySelector(".sr-result").innerHTML = "<h1>Payment Completed Successfully!</h1><p>After 5 seconds your page will reload and you should be able to watch your purchased video.  Refresh the page if this does not work.</p>";

    document.querySelector(".sr-result").classList.remove("hidden");
    setTimeout(function() {
      document.querySelector(".sr-result").classList.add("expand");
    }, 200);

    changeLoadingState(false);
  });
};

var orderFailed = function(errorMessage) {
  // Just for the purpose of the sample, show the PaymentIntent response object
    document.querySelector(".sr-payment-form").classList.add("hidden");
    document.querySelector(".sr-result").textContent = errorMessage;

    document.querySelector(".sr-result").classList.remove("hidden");
    setTimeout(function() {
      document.querySelector(".sr-result").classList.add("expand");
    }, 200);

    changeLoadingState(false);
};

var showError = function(errorMsgText) {
  changeLoadingState(false);
  var errorMsg = document.querySelector(".sr-field-error");
  errorMsg.textContent = errorMsgText;
  setTimeout(function() {
    errorMsg.textContent = "";
  }, 4000);
};

// Show a spinner on payment submission
var changeLoadingState = function(isLoading) {
  if (isLoading) {
    document.querySelector("button").disabled = true;
    document.querySelector("#spinner").classList.remove("hidden");
    document.querySelector("#button-text").classList.add("hidden");
  } else {
    document.querySelector("button").disabled = false;
    document.querySelector("#spinner").classList.add("hidden");
    document.querySelector("#button-text").classList.remove("hidden");
  }
};