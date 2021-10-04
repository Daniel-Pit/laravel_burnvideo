@extends('admin.layout')
@section('content')

<style>
    #setTextFaQ { width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px; resize:none; }
    #setTextPolicies { width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px; resize:none; }
    #setTextTerms { width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px; resize:none; }
    #setTextBurn { width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px; resize:none; }
</style>
       
<script>
    $(document).ready(function(){
        $("#setTextFaQ").wysihtml5();
        $("#setTextPolicies").wysihtml5();
        $("#setTextTerms").wysihtml5();
        $("#setTextBurn").wysihtml5();
    });
    
    
    function onSelectedOption()
    {
        var tSetting = $('#selectOption option:selected').val();
	
        if( tSetting == "faq" )
        {
            $("#faq_parent").show();
            $("#policies_parent").hide();
            $("#terms_parent").hide();
            $("#burn_parent").hide();
        }
        else if( tSetting == "policies" )
        {
            $("#faq_parent").hide();
            $("#policies_parent").show();
            $("#terms_parent").hide();
            $("#burn_parent").hide();
        }
        else if( tSetting == "terms" )
        {
            $("#faq_parent").hide();
            $("#policies_parent").hide();
            $("#terms_parent").show();
            $("#burn_parent").hide();
        }
        else if ( tSetting == "burn" )
        {
            $("#faq_parent").hide();
            $("#policies_parent").hide();
            $("#terms_parent").hide();
            $("#burn_parent").show();
        }
    }
   
    function onClickSet()
    {
        bootbox.confirm("Are you sure?", function(result) {
            if( result)
            {
                var url = "/admin/api_updateSet";
                var s_name  =   $("#selectOption option:selected").val();
                var data = "";
                
                if( s_name == "faq" )
                {
                    data =  $("#setTextFaQ").val();
                }
                else if( s_name == "policies" )
                {
                    data =  $("#setTextPolicies").val();
                }
                else if( s_name == "terms" )
                {
                    data =  $("#setTextTerms").val();
                }
                else if ( s_name == "burn" )
                {
                    data =  $("#setTextBurn").val();
                }
                
                $.post(url, { 's_name':s_name, 'data':data }, function(result){
                    if(result.status)
                    {                       
                        $.notify("Success update setting", { position: "bottom center",  className: 'success' });
                        //window.location.reload();
                    }
                    else
                    {
                        $.notify("Fail update setting", { position: "bottom center",  className: 'error' });
                    }
                });
            }
        });        
    }
</script>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Setting
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-cog"></i> Home</a></li>
                        <li class="active">Setting</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Setting</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div>
                                        <select id="selectOption" class="form-control" style="margin-bottom: 10px" onchange="onSelectedOption();">
                                            <option value="faq" default>FAQ</option>
                                            <option value="policies">Policies</option>
                                            <option value="terms">Terms</option>
                                            <option value="burn">What is Burn Video</option>
                                                
                                        </select>
                                    </div>
                                    <div id="faq_parent"  style="display:block">
                                        <textarea id="setTextFaQ" class="textarea" placeholder="Please input faq text.">
                                            {{ $faq }}
                                        </textarea>
                                    </div>
                                    
                                    <div id="policies_parent"  style="display:none">
                                        <textarea id="setTextPolicies" class="textarea" placeholder="Please input policies text.">
                                            {{ $policies }}
                                        </textarea>
                                    </div>
                                    
                                    <div id="terms_parent"  style="display:none">
                                        <textarea id="setTextTerms" class="textarea" placeholder="Please input terms text.">
                                            {{ $terms }}
                                        </textarea>
                                    </div>
                                    
                                    <div id="burn_parent" style="display:none">
                                        <textarea id="setTextBurn" class="textarea" placeholder='Please input "What is Burn Video" text.'>
                                            {{ $burn }}
                                        </textarea>
                                    </div>
                                    
                                    <div  style="margin:10px; text-align:center">
                                         <button class="btn btn-primary" style="min-width:200px" onclick='onClickSet();'> Set </button>
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>
                </section><!-- /.content -->    
@stop