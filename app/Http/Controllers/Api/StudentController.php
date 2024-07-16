<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    private function sendResponse($data, $status = 200)
    {
        return response()->json($data, $status);
    }

    private function sendError($message, $status)
    {
        return response()->json(['status' => $status, 'message' => $message], $status);
    }

    private function validateRequest($request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 422);
        }
        return null;
    }

    public function index()
    {
        $students = Student::all();
        if ($students->isEmpty()) {
            return $this->sendError('No records found!', 404);
        }
        return $this->sendResponse(['status' => 200, 'students' => $students]);
    }

    public function show(Request $request, int $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->sendError('No record found!', 404);
        }
        return $this->sendResponse(['status' => 200, 'data' => $student]);
    }

    public function store(Request $request)
    {
        $validationError = $this->validateRequest($request, [
            'name' => 'required|string|max:191',
            'course' => 'required|string|max:191',
            'email' => 'required|email|max:191',
        ]);

        if ($validationError)
            return $validationError;

        $student = Student::create($request->only(['name', 'course', 'email']));

        if ($student) {
            return $this->sendResponse(['status' => 201, 'message' => 'Student created successfully!'], 201);
        } else {
            return $this->sendError('Something went wrong!', 500);
        }
    }

    public function update(Request $request, int $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->sendError('Student id not found', 404);
        }

        $validationError = $this->validateRequest($request, [
            'name' => 'required|string|max:191',
            'course' => 'required|string|max:191',
            'email' => 'required|email|max:191',
        ]);

        if ($validationError)
            return $validationError;

        $student->update($request->only(['name', 'course', 'email']));
        return $this->sendResponse(['status' => 200, 'message' => 'Student updated successfully']);
    }

    public function destroy(Request $request, int $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->sendError('Student not found', 404);
        }

        $student->delete();
        return $this->sendResponse(['status' => 200, 'message' => 'Student deleted']);
    }
}