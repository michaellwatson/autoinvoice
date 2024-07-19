function calc_sub_total(q, p, t, n){
    var price = q*p;
    var tax = (price*t)/100;


    var wtax = parseFloat(tax).toFixed(2);
    var wtotal = parseFloat(price+tax).toFixed(2);
    console.log(wtotal+'#');

    if(!isNaN(wtax)){
        $('.tax_total_'+n).html(wtax);     
    }else{
        $('.tax_total_'+n).html(0.00); 
    }
    
    if(!isNaN(wtotal)){
        $('.line_total_'+n).html(wtotal);
    }else{
        $('.line_total_'+n).html(0.00);
    }
    calc_totals();
}

function calc_totals(){
    rowCount = $('#line_items tr').length;
    var x = 1;
    var total = 0;
    var tax = 0;
    while(x <= rowCount){
        tax+=parseFloat($('.tax_total_'+x).html());
        console.log(total);
        total+=parseFloat($('.line_total_'+x).html());
        console.log(total);
        x++;
    }

    var total = parseFloat(total).toFixed(2)
    if(!isNaN(total)){
        $('#total').html(total);
    }else{
        $('#total').html(0.00);
    }

    var tax = parseFloat(tax).toFixed(2);
    if(!isNaN(tax)){
        $('#taxtotal').html(tax);
    }else{
        $('#taxtotal').html(0.00);
    }

    var deposit = parseFloat($('#deposit').val());
    if(isNaN(deposit)){
        deposit = 0;
    }

    var amountToFinance = total-deposit;
    console.log(amountToFinance+'##');
    $('#amounttoFinance').html(parseFloat(amountToFinance).toFixed(2));

    var id = $('#interest_rate_selector').val();
    $(".finance").each(function() {
        console.log('@@@');
        console.log(id);
        var months = $(this).data('months');
        var interest = $(this).data('interest');
        var deposit = $('#deposit').val();

        finance_options($(this), total, months, interest, deposit, 0);
    });
}

$(document).ready(function() {

    $(document).on('keyup', '#total', function(){
    	
        var value = $(this).val();
        $('.price_1').val(value);
        total = value;
        
        //console.log(value);
        var deposit = $('#deposit').val();
        total = value-deposit;
        $('#amounttoFinance').html(total.toFixed(2));
        /*
        var id = $('#interest_rate_selector').val();
		
        $(".finance").each(function() {
            var months = $(this).data('months');
            var interest = $(this).data('interest');
            var deposit = $('#deposit').val();
            finance_options($(this), total, months, interest, deposit, id);
        });
        */
        $('#interest_rate_selector').trigger('change');
    });

    $(document).on('keyup', '#deposit', function(){
    	
        var deposit = $(this).val();
        console.log(value);
        var value = $('#total').val();
        total = value;
        
        total = value-deposit;
        $('#amounttoFinance').html(total.toFixed(2));
        /*
        var id = $('#interest_rate_selector').val();

        $(".finance").each(function() {
            var months = $(this).data('months');
            var interest = $(this).data('interest');
            var deposit = $('#deposit').val();
            finance_options($(this), total, months, interest, deposit, id);
        });
        */
        $('#interest_rate_selector').trigger('change');
    });
    /*
    $(document).on('change', '.quantity', function(){
        var n = $(this).attr('data-number');
        //alert(n);

        calc_sub_total($(this).val(), $('.price_'+n).val(), $('.tax_'+n).val(), n);
    });

    $(document).on('change', '.price', function(){
        var n = $(this).attr('data-number');
        //alert(n)

        calc_sub_total($('.quantity_'+n).val(), $(this).val(), $('.tax_'+n).val(), n);
    });

    $(document).on('change', '.tax', function(){
        var n = $(this).attr('data-number');
        //alert(n)

        calc_sub_total($('.quantity_'+n).val(), $('.price_'+n).val(), $(this).val(), n);
    });

    $(document).on('keyup', '#deposit', function(){
        calc_totals();
    });
    */
    
    $(document).on('change', '#interest_rate_selector', function(){

        var total = parseFloat($('#total').val()).toFixed(2)
        if(isNaN(total)){
            total = 0;
        }

        var id = $('#interest_rate_selector').val();
            
        var months = $(this).find('option:selected').data('months');
        var interest = $(this).find('option:selected').data('interest');
        var promocode = $(this).find('option:selected').data('promocode');
        //alert(promocode);

        var deposit = $('#deposit').val();

        $('.interest_rate').html(interest);
            
        $('.loan_title').html($("#interest_rate_selector option:selected").text());

        var old_id = $('.finance').data('id');

        $('#promocode_'+old_id).val(promocode);

        $(".finance").each(function() {
                
            console.log('months'+months);
            console.log('interest'+interest);
            console.log('deposit'+deposit);
            //update the element
            $(this).data('months', months);
            $(this).data('interest', interest);

            finance_options($(this), total, months, interest, deposit, old_id);
        });

        $.ajax({
            'url': url+'cart/calculate_subsidy_id/'+id,
            'method': 'POST',
            'dataType':'json',
        }).done(function (data) {

            subsidy = data.msg;

            var id = $('.finance').data('id');
            $('#subsidy_'+id).val(subsidy);
            $('#subsidy_desc_'+id).val('- '+data.description);

            var a = total-deposit;

            var subsidy_amount = ((a*subsidy)/100).toFixed(2);
            var subsidy_desc = $('#subsidy_desc_'+id).val();
            var nett = (total-subsidy_amount).toFixed(2);

            $('#subsidy_amount_'+id).html('<b>Subsidy</b><br>&pound;'+subsidy_amount+' '+subsidy_desc+'<br>Nett: &pound;'+nett);

        });

    });
     


    $(document).on("click", ".place_order", function(e){

        var method = $(this).data('method');

        e.preventDefault(); 
        
        var l = Ladda.create( document.querySelector('.place_order'));
        l.start();

        var post = [];

        $('.in').removeClass('form-error');
        window.scrollTo(0, 0);

        var x = 1;
        var subTotal = 0;
        //rowCount = $('#line_items tr').length;
        rowCount = 1;

        var error = false;

        while(x <= rowCount){
            
            var quantity = $('.quantity_'+x).val();
            if(quantity==''){
                $('.quantity_'+x).addClass('form-error');
                $('.alert_message').show();
                $('.alert_message').append('quantity_'+x);
                error = true;
            }
            
            var item = $('.item_'+x+' option:selected').text();
            //alert(item);

            if(item==''){
                $('.item_'+x).addClass('form-error');
                $('.alert_message').show();
                $('.alert_message').append('item_'+x);
                error = true;
            }
            
            var unit_price = $('#total').val();
            if(unit_price==''){
                $('.price_'+x).addClass('form-error');
                $('.alert_message').show();
                $('.alert_message').append('price_'+x);
                error = true;
            }

            var tax = $('.tax_'+x).val();
            if(tax==''){
                $('.tax_'+x).addClass('form-error');
                $('.alert_message').show();
                $('.alert_message').append('tax_'+x);
                error = true;
            }

            if(typeof quantity !== 'undefined') {
	            console.log(quantity+'#');
	            console.log(item+'#');
	            console.log(unit_price+'#');
	            console.log(tax+'#');
	            var tax_amount = (unit_price*tax)/100;

	            post.push({quantity, item, unit_price, tax, tax_amount});
	            subTotal+=(quantity*unit_price);
        	}
            x++;
        }

        console.log('###');
        console.log(subTotal);

  
        if($('#mobile').val()==''){
            $('#mobile').addClass('form-error');
            error = true;
        }
        if($('#email').val()==''){
            $('#email').addClass('form-error');
            error = true;
        }

        
        if($('#patient_full_name').val()==''){
            $('#patient_full_name').addClass('form-error');
            error = true;
        }
        if($('#treatment_reference').val()==''){
            $('#treatment_reference').addClass('form-error');
            error = true;
        }

        var total = $('#total').val();
        var taxtotal = 0;
        
        if(error == true){
            l.stop();
        }else{

            $.ajax({
                'url': url+'cart/create_session_basic',
                'method': 'POST',
                'dataType':'json',
                'data': {   
                    'data' : post, 
                    'total' : total, 
                    'taxtotal' : taxtotal, 
                    'subtotal': subTotal, 
                    'deposit': $('#deposit').val(), 
                    'mobile': $('#mobile').val(), 
                    'email': $('#email').val(), 
                    'patient_title': $('#patient_title').val(), 
                    'patient_first_name': $('#patient_first_name').val(), 
                    'patient_last_name': $('#patient_last_name').val(), 
                    'treatment_reference': $('#treatment_reference').val(), 
                    'merchant_reference': $('#merchant_reference').val(), 
                    'promocode': $('.promocode').val(),
                    'method': method,
                }
            }).done(function (response) {
                l.stop();
                
                if(response.status==1){
                    window.location = url+'cart/session_sent';
                }else{
                    $('.alert_message').html(response.msg);
                    $('.alert_message').show();
                }
                
            });

        }

    });



    $(document).on("click", ".radio_link_to_phone", function(){ 
        $('.link_to_phone').show();
        $('.link_to_email').hide();
        $('#email').val('');
        is_mandatory_info_filled();
    });

    $(document).on("click", ".radio_link_to_email", function(){ 
        $('.link_to_email').show();
        $('.link_to_phone').hide();
        $('#mobile').val('');
        is_mandatory_info_filled();
    });

    $(document).on("change", "#depositPercentage", function(){ 
        var purchasePrice = $('#purchasePrice').val()*$(this).val();
        $('#deposit').val(purchasePrice);
        $("#purchasePrice").trigger('keyup');
    });

    $(document).on("keyup", "#deposit", function(){ 
        $("#purchasePrice").trigger('keyup');
    });
    //calculations page
    //Three parameters: amount, months, interest rate (percent)
    //finance.calculatePayment(25000, 60, 5.25);
    $(document).on("keyup", "#purchasePrice", function(){ 
        var amount = $(this).val();
        
        /*
        var princ = document.calc.loan.value;
        var term  = document.calc.months.value;
        var intr   = document.calc.rate.value / 1200;
        document.calc.pay.value = princ * intr / (1 - (Math.pow(1/(1 + intr), term)));
        */
        var count = 1;
        $(".finance").each(function() {
            var id = $(this).data('id');
            var months = $(this).data('months');
            var interest = $(this).data('interest');
            var deposit = $('#deposit').val();

            finance_options($(this), amount, months, interest, deposit, id);
            count++;
        
        });
         
    });

    $(document).on("keyup", "#patient_full_name", function(){ 
        is_mandatory_info_filled();
    });
    $(document).on("keyup", "#treatment_reference", function(){ 
        is_mandatory_info_filled();
    });
    $(document).on("keyup", "#mobile", function(){ 
        is_mandatory_info_filled();
    });
    $(document).on("keyup", "#email", function(){ 
        is_mandatory_info_filled();
    });
    
    $(document).on('click', '.btn_select', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        href = href+'?amount='+$('#purchasePrice').val()+'&deposit='+$('#deposit').val();
        window.location = href;
    });

});

function is_mandatory_info_filled(){
    var patient_full_name   = $('#patient_full_name').val();
    var treatment_reference = $('#treatment_reference').val();
    var mobile              = $('#mobile').val();
    var email               = $('#email').val();

    if((patient_full_name!='')&&(treatment_reference!='')&&(mobile!='')&&(email!='')){
        $('.place_order').removeClass('disabled');
    }else{
        $('.place_order').addClass('disabled');
    }
}

function finance_options($this, amount, months, interest, deposit, count){
    console.log('A:'+amount);
    console.log('D:'+deposit);
    console.log('M:'+months);

    $('#total_cost_'+count).html(amount);
    $('#amount_to_finance_'+count).html(amount-deposit);

    $this.find('.loan_duration').html(months);
    
    if(deposit!=''){
        var payment = finance.calculatePayment((amount-deposit), months, interest);
        console.log('#'+payment+'#');
        $('#total_payable_'+count).html((parseFloat(months*payment)+parseFloat(deposit)).toFixed(2));
    }else{
        var payment = finance.calculatePayment((amount), months, interest);
        $('#total_payable_'+count).html((parseFloat(months*payment)).toFixed(2));
    }
    console.log('P:'+payment);

    if(deposit!=''){
        console.log('###');
        var months_payment = months*payment;
        console.log(months_payment);

        var months_payment_less_amount = months_payment-parseFloat(amount-deposit);
        console.log(months_payment_less_amount);

        //var months_payment_less_amount_deposit = months_payment_less_amount-parseFloat(deposit);
        if(months_payment_less_amount<0){
        	$('#costofcredit_'+count).html(0.00);
        }else{
        	$('#costofcredit_'+count).html(months_payment_less_amount.toFixed(2));
        }
        
        $('#amount_'+count).html(payment.toFixed(2));
        $('#deposit_'+count).html(deposit);
    }else{
        console.log('##');
        $('#costofcredit_'+count).html(((months*payment)-(parseFloat(amount)-0)).toFixed(2));
        $('#amount_'+count).html(payment.toFixed(2));
        $('#deposit_'+count).html(0.00);
    }

    
    $('#subsidy_amount_'+count).html()

    var subsidy = $('#subsidy_'+count).val();

    var a = amount;

    var subsidy_amount = ((a*subsidy)/100).toFixed(2);
    var subsidy_desc = $('#subsidy_desc_'+count).val();

    var nett = (amount-subsidy_amount).toFixed(2);

    $('#subsidy_amount_'+count).html('<b>Subsidy</b><br>&pound;'+subsidy_amount+' '+subsidy_desc+'<br>Nett: &pound;'+nett);
    
    
    if(!$this.find('.btn_select').hasClass('place_order')){
        $this.find('.btn_select').removeClass('disabled');
    }
}

$(document).ready(function() {
    //alert('hit');
    if($('#patient_full_name').length>0){
        $('#total').trigger('keyup');
        $('#deposit').trigger('keyup');
    }
});

$(document).ready(function(){
    //user autocomplete 
    var treatments = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: url+'Treatments/listTreatments/?text=%QUERY'
    });

    treatments.initialize();

    $('.item').typeahead(null, {
        name: 'treatments',
        displayKey: 'value',
        valueKey: 'id',
        source: treatments.ttAdapter()
    }).on('typeahead:selected', function(event, data) {
        //alert(data.id);     
        //$('#userid').val(data.id);
        //paginate(0);
        //alert(data.value);
        //alert(data.id);
        $(".item").val(data.value);
    });

    $(document).on("click", ".select_all", function(){ 
        //alert('hit');
        var checkboxes = $(this).closest('form').find(':checkbox');
        checkboxes.prop('checked', $(this).is(':checked'));
    });

});

$(document).ready(function() {
    $('input[type=checkbox]').on('click', function() {
        if($(this).is(':checked')) {
            $(this).parent().parent().addClass('product_selected');
        }else{
            $(this).parent().parent().removeClass('product_selected')
        }
    });
});