<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User, StudentProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminCsvImportController extends Controller
{
    // ── Show import page ──────────────────────────────────────
    public function index()
    {
        return view('admin.import.index');  // ← was 'coordinator.import.index'
    }

    // ── Download CSV template ──────────────────────────────────
    public function template(string $type)
    {
        if ($type === 'students') {
            $headers = ['name','email','password','student_id','course',
                        'year_level','section','required_hours','phone','address'];
        } elseif ($type === 'companies') {
            $headers = ['name','address','contact_person','contact_email',
                        'contact_phone','industry'];
        } else {
            abort(404, 'Unknown template type.');
        }

        $filename = "ojt_{$type}_import_template.csv";

        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            if ($headers[0] === 'name') {
                fputcsv($out, count($headers) === 10
                    ? ['Juan Dela Cruz','juan@buksu.edu.ph','Password123',
                       'STU-001','BS Information Technology','3rd Year',
                       '3A','486','09171234567','Malaybalay City']
                    : ['ABC Company','123 Main St','Jane Smith',
                       'jane@abc.com','09180000000','IT/Technology']
                );
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // ── Process uploaded CSV ───────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'type'   => ['required', 'in:students,companies'],
            'file'   => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $type    = $request->type;
        $file    = $request->file('file');
        $handle  = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);

        $results = ['created' => 0, 'skipped' => 0, 'errors' => []];
        $row = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if ($type === 'students') {
                $this->importStudent($data, $row, $results);
            } else {
                $this->importCompany($data, $row, $results);
            }
        }
        fclose($handle);

        $msg = "Import complete. Created: {$results['created']}, Skipped: {$results['skipped']}.";

        return back()->with('import_results', $results)->with('success', $msg);
    }

    // ── Import one student row ─────────────────────────────────
    private function importStudent(array $data, int $row, array &$results): void
    {
        if (count($data) < 6) {
            $results['errors'][] = "Row {$row}: Not enough columns.";
            $results['skipped']++;
            return;
        }

        [$name, $email, $password, $studentId, $course,
         $yearLevel, $section, $reqHours, $phone, $address] = array_pad($data, 10, null);

        $v = Validator::make(
            ['name' => $name, 'email' => $email, 'course' => $course],
            [
                'name'   => 'required|string|max:255',
                'email'  => 'required|email|unique:users,email',
                'course' => 'required|string',
            ]
        );

        if ($v->fails()) {
            $results['errors'][] = "Row {$row}: " . implode(', ', $v->errors()->all());
            $results['skipped']++;
            return;
        }

        $user = User::create([
            'name'      => trim($name),
            'email'     => strtolower(trim($email)),
            'password'  => Hash::make($password ?: 'ChangeMe123!'),
            'role'      => 'student_intern',
            'is_active' => true,
        ]);

        StudentProfile::create([
            'user_id'        => $user->id,
            'student_id'     => $studentId ?? 'CSV-' . $user->id,
            'firstname'      => explode(' ', $name)[0],
            'lastname'       => implode(' ', array_slice(explode(' ', $name), 1)) ?: $name,
            'course'         => $course,
            'year_level'     => $yearLevel ?? '1st Year',
            'section'        => $section ?? '',
            'required_hours' => (int)($reqHours ?? 486),
            'phone'          => $phone,
            'address'        => $address,
        ]);

        $results['created']++;
    }

    // ── Import one company row ─────────────────────────────────
    private function importCompany(array $data, int $row, array &$results): void
    {
        if (count($data) < 1 || empty(trim($data[0]))) {
            $results['errors'][] = "Row {$row}: Company name is required.";
            $results['skipped']++;
            return;
        }

        [$name, $address, $contactPerson, $contactEmail,
         $contactPhone, $industry] = array_pad($data, 6, null);

        if (Company::where('name', trim($name))->exists()) {
            $results['errors'][] = "Row {$row}: Company '{$name}' already exists.";
            $results['skipped']++;
            return;
        }

        Company::create([
            'name'           => trim($name),
            'address'        => $address,
            'contact_person' => $contactPerson,
            'contact_email'  => $contactEmail,
            'contact_phone'  => $contactPhone,
            'industry'       => $industry,
            'is_active'      => true,
        ]);

        $results['created']++;
    }
}