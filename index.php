<?php
require 'vendor/autoload.php'; // Include the Stripe PHP library

\Stripe\Stripe::setApiKey('your_stripe_secret_key'); // Replace with your Stripe secret key

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $token = $_POST['stripeToken'];
        $charge = \Stripe\Charge::create([
            'amount' => 1000, // Amount in cents
            'currency' => 'usd',
            'description' => 'Example charge',
            'source' => $token,
        ]);
        // Payment successful, you can save the charge information to your database or perform other actions here
    } catch (\Stripe\Error\Card $e) {
        // Handle card errors
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Stripe Payment Example</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Make a Payment</h1>

    <?php if (isset($error)): ?>
        <p>Error: <?php echo $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="card-element">
            Credit or debit card
        </label>
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>

        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>

        <button type="submit">Submit Payment</button>
    </form>

    <script>
        var stripe = Stripe('your_stripe_public_key'); // Replace with your Stripe public key
        var elements = stripe.elements();

        var card = elements.create('card');
        card.mount('#card-element');

        card.on('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to your server
            var form = document.querySelector('form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    </script>
</body>
</html>
