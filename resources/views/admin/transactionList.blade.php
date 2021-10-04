@extends('admin.layout')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Transaction List
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-usd"></i> Home</a></li>
        <li class="active">Transaction</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Transaction</h3>                                    
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table id="objectTable" data-order='[[ 0, "desc" ]]' class="display table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>E-Mail</th>
                                <th>Machine</th>
                                <th>Order</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Promocode</th>
                                <th>Final Price</th>
                                <th>Pay Time</th>                                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $idx=>$item)
                            <tr>
                                <td>{{ $idx + 1  }}</td>
                                <td>{{ $item->first_name . ' ' . $item->last_name }}</td>
                                <td>{{ $item->email  }}</td>
                                <td>{{ $item->devicestr }}</td>
                                <td>{{ $item->ordertag }}</td>
                                <td>{{ $item->ttype }}</td>
                                <td>${{ $item->price }}</td>
                                <td>
                                    @if($item->promocode_id)
                                    {{$item->name}}
                                    @else
                                    --
                                    @endif
                                </td>
                                <td>
                                    @if($item->promocode_id)
                                    ${{$item->final_price}}
                                    @else
                                    ${{ $item->price }}
                                    @endif
                                </td>
                                <td>{{ date('n-j-Y', $item->paytime) }}</td>                                               
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.box-body -->				
            </div><!-- /.box -->
        </div>
    </div>
</section><!-- /.content -->    
@stop