<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cooperative;
use App\Models\EmailCredentials;
use App\Models\Member;
use App\Traits\EmailTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\CsvTrait;

class MemberController extends Controller
{
    use EmailTrait, CsvTrait;

    /**
     * Create new member
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {

        $validate = $this->validator($request);
        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages()
            ], 400);
        }

        $member = new Member();
        $this->store($request, $member);

        $member->password = randomPassword();

        $send = $this->sendWelcomeEmail($member);
        if (!$send) {
            $member->forceDelete();

            return response([
                'status' => false,
                'message' => 'Could not send welcome email'
            ], 500);
        }

        $member->password = Hash::make($member->password);
        $member->save();

        unset($member->password);

        return response([
            'status' => true,
            'message' => 'Member Created',
            'data' => $member
        ], 201);
    }

    /**
     * Member data validator
     * @param Request $request
     * @param array $customRules
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request, $customRules = [])
    {
        $genders = ['male', 'female', 'other'];

        return Validator::make($request->all(), [
            'firstname' => $customRules['firstname'] ?? 'required|string|max:50',
            'lastname' => $customRules['lastname'] ?? 'required|string|max:50',
            'othernames' => $customRules['othernames'] ?? 'max:100',
            'email' => $customRules['email'] ?? 'required|email:filter,rfc,dns|unique:members',
            'phone' => $customRules['phone'] ?? 'required|max:15',
            'gender' => [
                'required',
                Rule::in($genders)
            ]
        ]);
    }

    /**
     * Store member data
     * @param Request $request
     * @param Member $member
     *
     * @return void
     */
    public function store($request, $member)
    {
        $member->firstname = ucfirst($request->firstname);
        $member->lastname = ucfirst($request->lastname);
        $member->othernames = ucfirst($request->othernames ?? '');
        $member->email = strtolower($request->email);
        $member->phone = $request->phone;
        $member->gender = $request->gender;

        $member->save();
    }

    /**
     * Send welcome email to new member
     * @param Member $member
     *
     * @return bool
     */
    public function sendWelcomeEmail($member)
    {
        $emailSubject = 'Welcome to Coopco';
        $emailTemplate = 'welcomeMember';

        $cooperative = Cooperative::firstOrFail()->name;

        $emailData = [
            'cooperative' => $cooperative,
            'password' => $member->password
        ];

        return $this->sendSingleEmail($emailSubject, $member->email, $emailData, $emailTemplate);
    }

    /**
     * Soft Delete member data
     * @param int $memberId Member ID
     * @return Response
     */
    public function delete(int $memberId)
    {
        $member = Member::findOrFail($memberId);

        $member->delete();

        return response([
            'status' => true,
            'message' => 'Member Deleted'
        ], 200);
    }

    /**
     * Activate/Deactivate member
     * @param int $memberId Member ID
     * @param string $status activate or deactivate
     *
     * @return Response
     */
    public function updateStatus(int $memberId, $status = true)
    {
        if ($status !== 'activate' && $status !== 'deactivate') {
            return response([
                'status' => true,
                'message' => g('NOT_FOUND')
            ], 404);
        }

        $member = Member::findOrFail($memberId);

        $member->active = $status == 'activate';
        $member->save();

        return response([
            'status' => true,
            'message' => 'Member ' . ucfirst($status) . 'd'
        ], 200);
    }

    /**
     * Get all members in batch of 20
     *
     * @return json
     */
    public function getAll()
    {
        $members = Member::paginate(2);

        return response([
            'status' => true,
            'message' => 'Successful',
            'data' => $members
        ], 200);
    }

    /**
     * Get one member
     * @param int $memberId Member ID
     * @return Response
     */
    public function getOne(int $memberId)
    {

        $member = Member::findOrFail($memberId);

        return response([
            'status' => true,
            'message' => 'Successful',
            'data' => $member
        ], 200);
    }

    /**
     * Update member
     * @param Request $request
     * @param int $memberId Member Id
     * @return Response
     */
    public function update(Request $request, int $memberId)
    {

        // Validate form fields
        $validate = $this->validator($request, [
            'email' => [
                'required', 'email:filter,rfc,dns',
                Rule::unique('members')->ignore($memberId)
            ]
        ]);

        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages()
            ], 400);
        }

        $member = Member::findOrFail($memberId);
        $this->store($request, $member);

        return response([
            'status' => true,
            'message' => 'Member Updated',
            'data' => $member
        ], 200);
    }

    /**
     * Search members
     * @param Request $request
     *
     * @return json
     */
    public function search(Request $request)
    {
        $keyword = strtolower($request->get('keyword'));
        $members = Member::where(DB::raw('lower(firstname)'), 'LIKE', '%' . $keyword . '%')
            ->orWhere(DB::raw('lower(lastname)'), 'LIKE', '%' . $keyword . '%')
            ->orWhere(DB::raw('lower(othernames)'), 'LIKE', '%' . $keyword . '%')
            ->orWhere(DB::raw('lower(email)'), 'LIKE', '%' . $keyword . '%')
            ->orWhere('phone', 'LIKE', '%' . $keyword . '%')
            ->paginate(2);

        return response([
            'status' => true,
            'message' => 'Successful',
            'data' => $members
        ], 200);
    }

    /**
     * Create more than one member
     * 
     * @param Request $request
     * 
     * @return void
     *  
     */
    public function createBulk(Request $request)
    {
        $csvFile = base64ToFile($request->csv);
        $realPath = $csvFile->getRealPath();

        // Check the number of records in the CSV file
        $fp = file($realPath, FILE_SKIP_EMPTY_LINES);
        if (count($fp) > 250) {
            return response([
                'status' => false,
                'errors' => 'The csv file must contain at most 250 records.'
            ], 400);
        }

        // Validate contents of csv file
        $validate = $this->validateBulk($csvFile);
        if (!$validate['status']) {
            return response([
                'status' => false,
                'errors' => $validate['messages']
            ], 400);
        }

        // Store data
        $emailData = $this->storeBulk($validate);

        // Send bulk email to new members
        $this->sendBulkEmail('Welcome', $emailData, 'bulk-test');

        return response([
            'status' => true,
            'message' => 'Successful',
        ], 201);
    }

    /**
     * Validate csv data
     * 
     * @param Illuminate\Http\UploadedFile $file
     * 
     * @return array - An array of the validation message and ststus
     */
    public function validateBulk($file)
    {
        $rules = [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'othernames' => 'max:100',
            'email' => 'required|email:filter,rfc,dns|unique:members',
            'phone' => 'required|max:15',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
        ];
        $res = $this->validateCSVFile($rules, $file);

        return $res;
    }

    /**
     * Store bulk member data
     * 
     * @param array $validate - The validated data
     * 
     * @return array $emailData
     */
    public function storeBulk($validate)
    {
        $userData = $validate['data'];

        $emailData = array();

        foreach ($userData as $key => $data) {
            Member::create($data);

            // Add new member email data to email data array
            $emailData[$data['email']] = $data;
        }

        return $emailData;
    }
}
