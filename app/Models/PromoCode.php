<?php
namespace burnvideo\Models;
use Eloquent;
use Validator;

class PromoCode extends Eloquent {

    protected $table = 'promocode';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'type', 'value', 'expiry_date', 'description'];

    public static function validate($data) {


        $rule = array(
            'name' => 'required|unique:promocode',
            'type' => 'required',
            'value' => 'required',
            'expiry_date' => 'required',
            'description' => 'required',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
            'unique' => 'The promocode already Exist.',
        );


        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

    public static function validateUpdate($data, $id) {

        $rule = array(
            'name' => 'required|unique:promocode,name,' . $id . ',id',
            'type' => 'required',
            'value' => 'required',
            'expiry_date' => 'required',
            'description' => 'required',
        );

        $messages = array(
            'required' => 'The :attribute field is required.',
            'unique' => 'The promocode already Exist.',
        );

        $data = Validator::make($data, $rule, $messages);
        return $data;
    }

}
