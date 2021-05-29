<div class="card_stripe">
    <form action="https://easypay.easypaisa.com.pk/easypay/Confirm.jsf" method="POST" id="easypaisaConfirmForm">
        <input type="text" name="auth_token" value="{{$data['auth_token']}}">
        <input type="text" name="postBackURL" value="{{$data['postBackURL']}}">

        <div class="card-body p-3">   
            <h2>Pay With</h2>
            <br>
            <div class="text-right">
                <a href="index.php" id="payBtn" class="btn btn-primary py-2">Back</a> 
                <input type="submit" id="payBtn" class="btn btn-info py-2" value="Proceed to Checkhout">
            </div>
        </div>
    </form>
</div>