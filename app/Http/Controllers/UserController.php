<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


use App\Models\User;
use App\Models\FollowQuestion;
use App\Models\Notification;
use App\Models\BadgeAttainmentNotification;
use App\Models\Question;
use App\Models\UnblockAccount;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{

    public function updateUser(Request $request){
        $userId = $request->input('user_id');
        $userAuth = Auth::user();
        $this->authorize('edit', $userAuth);
        $user = User::find($userId);

        $request->validate(['name' => 'required|string|max:255',
        ]);

        if($user->username !== $request->input('username')){
            $request->validate(['username' => 'required|string|max:16|unique:appuser']);
            $user->username = $request->input('username');
            
        }
        $user->name = $request->input('name');
        if($user->email !== $request->input('email') ){
            $request->validate(['email' => 'required|email|max:40|unique:appuser']);
            $user->email = $request->input('email');
        }

        $new_password = $request->input('password');
        if( strlen($new_password )!== 0){
            $request->validate(['password' => 'required|string|min:8']);
            $user->password = Hash::make($request->input('password'));
        }

        $new_paylink = $request->input('paylink');
        if( strlen($new_paylink )!== 0){
            $request->validate(['paylink' => 'url']);
            $user->paylink = $request->input('paylink');
        }
        
        $user->bio = $request->input('bio');

        $user->paylink = $request->input('paylink');

        $user->save();

        return redirect()->route('editprofile', ['id' => $user->id]);


    }

    public function updateUserAdmin(Request $request){

        $userId = $request->input('user_id');
        $userAuth = Auth::user();
        $this->authorize('editadmin', $userAuth);

        $user = User::find($userId);
        
        $user->usertype = $request->input('usertype');
        
        $badges = $request->input('badges');
        $userBadges = $user->badges()->get();
        if($badges){
            $badgeData = [];
            $date = date('Y-m-d H:i:s'); 
            foreach($badges as $badge) {
                $badgeData[$badge] = ['date' => $date];
            }
            $user->badges()->sync($badgeData);
            foreach($userBadges as $t){
                if( $t->id !== $badge){
                    $notification = Notification::create([
                        'user_id' => $user->id,
                        'date' => $date
                    ]);
                    $notification->save();
                    DB::table('badgeattainmentnotification')->insert(
                        ['user_id' => $user->id,'badge_id' => $badge,'notification_id' => $notification->id]
                    );
                    break;
                }
            }
        }
    
        $user->save();

        return redirect()->route('editprofile', ['id' => $user->id]);
    }


    public function deleteAccount(Request $request,string $id)
    {
        $userBeingDeleted = User::find($id);

        $this->authorize('delete', $userBeingDeleted);
        TransactionsController::deleteUser($userBeingDeleted->id);
        if($id === Auth::user()->id){
            return redirect()->route('logout');
        }
        else{
            return redirect()->route('users');
        }
        
    }


    public function blockAccount(Request $request,string $id)
    {
        $userBeingBlocked = User::find($id);
        $this->authorize('block', $userBeingBlocked);
        if($userBeingBlocked->blocked === true){
            $userBeingBlocked->blocked = false;
        }
        else{
            $userBeingBlocked->blocked = true;
        }
        $userBeingBlocked->save();
        
        return redirect()->route('users');
    }
       
    public function unblockaccountform(Request $request,string $id)
    {
        $userBeingUnblocked = User::find($id);
        $this->authorize('unblockform', $userBeingUnblocked);
        return view('auth.unblockaccount', ['user' => $userBeingUnblocked]);
    }

    public function unblockaccountrequest(Request $request,string $id)
    {
        $userBeingUnblocked = User::find($id);
        $this->authorize('unblockform', $userBeingUnblocked);
        $request->validate(['appeal' => 'required|string|max:255']);

        $existingRequest = UnblockAccount::where('user_id', $userBeingUnblocked->id)->first();
        if ($existingRequest) {
            return redirect()->route('unblockaccountform', ['id' => $userBeingUnblocked->id])
                ->with('message', 'Unblock request already exists.');
        }

        $unblockAccount = UnblockAccount::create([
            'user_id' => $userBeingUnblocked->id,
            'appeal' => $request->input('appeal')
        ]);
        $unblockAccount->save();
        return redirect()->route('profile', ['id' => $userBeingUnblocked->id]);
    }

    public function reviewaccount(Request $request,string $id)
    {
        $userReviewing = Auth::user();
        $this->authorize('review', $userReviewing);
        $unblockAccount = UnblockAccount::where('id',$id)->with(['user'])->first();
        if($unblockAccount === null){
            return redirect()->route('moderateusers')->withErrors(['unblockaccount'=>'The provided unblock account request does not exist']);
        }
        return view('pages.reviewaccount', ['unblockaccount' => $unblockAccount]);
    }

    public function processaccount(Request $request)
    {
        $userReviewing = Auth::user();
        $id = $request->input('unblock_request_id');
        $this->authorize('process', $userReviewing);
        $unblockAccount = UnblockAccount::where('id',$id)->with(['user'])->first();
        if($unblockAccount === null){
            return redirect()->route('moderateusers')->withErrors(['unblockaccount'=>'The provided unblock account request does not exist']);
        }
        $userBeingReviewed = $unblockAccount->user;
        if($userBeingReviewed === null){
            return redirect()->route('moderateusers')->withErrors(['user'=>'The provided user account does not exist']);
        }
        $action = $request->input('action');
        if($action=== 'unblock'){
            $userBeingReviewed->blocked = false;
            $userBeingReviewed->save();
        }
        elseif($action=== 'keep_blocked'){
            $userBeingReviewed->blocked = true;
            $userBeingReviewed->save();
        }
        $unblockAccount->delete();
        return redirect()->route('moderatecontent');
    }
}
