<!DOCTYPE html>
<html>
<link href="https://fonts.googleapis.com/css2?family=Sora&display=swap" rel="stylesheet"> 
<style type="text/css">
    .widt-req {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-family: 'Sora', sans-serif;
    }
    .widt-req form {
        display: flex;
        flex-direction: column;
        width: 30%;
        background: #fff;
        padding: 60px;
        box-shadow: 0 0 16px rgb(0 0 0 / 10%);
        border-radius: 25px;
    }
    .widt-req h4{font-size: 28px;}
    label{font-size: 16px; font-weight: 600; margin-bottom: 10px;}
    input{font-size: 16px; border: 1px solid #000;height: 40px;padding: 5px 10px; border-radius: 10px; margin-bottom: 15px; }
    textarea{font-size: 16px; border: 1px solid #000;height: 80px;padding: 5px 10px; border-radius: 10px; margin-bottom: 15px; }
    .submit{display: inline-block; background: #000; width: auto; color: #fff; width: 150px; margin: 0 auto; cursor: pointer;}
</style>
<div class="widt-req">
    <h4>Withdrawal Request</h4>
    
    <form action="{{HTTP_PATH}}/merchat-withdrawal" method="post">
        
        <input type="hidden" name="merchant_key" value="F5QODftWkr9U">
        <label>Order ID :</label>
        <input type="text" name="order_id" value="Order-0709" placeholder="" disable>
        <label>Email :</label>
        <input type="text" name="user_email" value="" placeholder="">
        <label>Amount :</label>
        <input type="text" name="amount" value="100" maxlength="10">
        <label>Remark :</label>
        <textarea type="text" name="remark" value="Testing for hidden"></textarea>
        <input type="hidden" name="return_url" value="https://www.nimbleappgenie.live/dafri/success">
        <input type="submit" name="submit" value="submit" class="submit">
    </form>
    
</div>

</html>