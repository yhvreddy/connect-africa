<script>
    $(document).ready(function() {
        // Load Subscription Types based on Subscription selection
        $('#subscription').change(function() {
            let subscription_id = $(this).val();
            getSubscriptionTypes(subscription_id);
        });

        // Load Subscription Plans based on Subscription Type selection
        $('#subscription_type').change(function() {
            let subscription_type_id = $(this).val();
            getSubscriptionPlans(subscription_type_id);
        });
    });

    function getSubscriptionTypes(subscription_id) {
        if (subscription_id) {
            $.ajax({
                url: "{{ route('get.subscription.types') }}",
                type: "GET",
                data: {
                    subscription: subscription_id
                },
                success: function(response) {
                    console.log(response.data);
                    $('#subscription_type').html(
                        '<option value="">Select Type</option>');
                    $('#subscription_plan').html('<option value="">Select Plan</option>');

                    $.each(response.data, function(key, value) {

                        $('#subscription_type').append('<option value="' + value.id + '">' + value
                            .name + '</option>');

                    });

                    getPaymentMethods(subscription_id);
                }
            });
        } else {
            $('#subscription_type').html('<option value="">Select Type</option>');
            $('#subscription_plan').html('<option value="">Select Plan</option>');
            $('#subscription_payment_method').html(
                '<option value="">Select Payment Method</option>');
        }
    }

    function getSubscriptionPlans(subscription_type_id) {
        if (subscription_type_id) {
            $.ajax({
                url: "{{ route('get.subscription.plans') }}",
                type: "GET",
                data: {
                    subscriptionTypeId: subscription_type_id
                },
                success: function(response) {
                    $('#subscription_plan').html(
                        '<option value="">Select Plan</option>');
                    $.each(response.data, function(key, value) {
                        $('#subscription_plan').append('<option value="' + value
                            .id +
                            '" ((selectedSubscriptionTypeId == value.id)?"selected":"")  >' +
                            value.name + '</option>');
                    });
                }
            });
        } else {
            $('#subscription_plan').html('<option value="">Select Plan</option>');
            $('#subscription_payment_method').html(
                '<option value="">Select Payment Method</option>');
        }
    }

    function getPaymentMethods(subscription_id) {
        if (subscription_id) {
            $.ajax({
                url: "{{ route('get.subscription.payment.methods') }}",
                type: "GET",
                data: {
                    subscription: subscription_id
                },
                success: function(response) {
                    $('#subscription_payment_method').html(
                        '<option value="">Select Payment Method</option>');
                    $.each(response.data, function(key, value) {
                        $('#subscription_payment_method').append(
                            '<option value="' + value.id + '">' + value
                            .payment_method.name + '</option>');
                    });
                }
            });
        } else {
            $('#subscription_payment_method').html(
                '<option value="">Select Payment Method</option>');
        }
    }
</script>
