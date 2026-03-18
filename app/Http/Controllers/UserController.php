<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

if ($request->role === 'student') {
    $request->validate([
        'student_id'     => 'required|string|unique:student_profiles,student_id',
        'course'         => 'required|string',
        'year_level'     => 'required|string',
        'section'        => 'required|string',
        'phone'          => 'nullable|string',
        'address'        => 'nullable|string',
        'required_hours' => 'nullable|integer',
    ]);

    $user->studentProfile()->create([
        'student_id'     => $request->student_id,
        'course'         => $request->course,
        'year_level'     => $request->year_level,
        'section'        => $request->section,
        'phone'          => $request->phone,
        'address'        => $request->address,
        'required_hours' => $request->required_hours ?? 490,
    ]);
}