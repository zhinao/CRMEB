<?php


use think\facade\Route;

Route::get('crud/qa', 'crud.Qa/index')->option(['real_name' => '问答列表列表接口']);

Route::get('crud/qa/create', 'crud.Qa/create')->option(['real_name' => '问答列表获取创建表单接口']);

Route::post('crud/qa', 'crud.Qa/save')->option(['real_name' => '问答列表保存接口']);

Route::get('crud/qa/:id/edit', 'crud.Qa/edit')->option(['real_name' => '问答列表获取修改表单接口']);

Route::put('crud/qa/:id', 'crud.Qa/update')->option(['real_name' => '问答列表修改接口']);

Route::put('crud/qa/status/:id', 'crud.Qa/status')->option(['real_name' => '问答列表修改状态接口']);

Route::delete('crud/qa/:id', 'crud.Qa/delete')->option(['real_name' => '问答列表删除接口']);

Route::get('crud/qa/:id', 'crud.Qa/read')->option(['real_name' => '问答列表查看接口']);



