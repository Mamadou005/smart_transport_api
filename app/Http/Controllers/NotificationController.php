<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller {

    public function index(Request $request) {
        $notifications = Notification::where('user_id',
            $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json($notifications);
    }

    public function marquerLue(Request $request, $id) {
        Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->update(['lue' => true]);

        return response()->json(['message' => 'Notification lue']);
    }

    public function marquerToutesLues(Request $request) {
        Notification::where('user_id', $request->user()->id)
            ->where('lue', false)
            ->update(['lue' => true]);

        return response()->json(['message' => 'Toutes lues']);
    }

    public function nonLues(Request $request) {
        $count = Notification::where('user_id',
            $request->user()->id)
            ->where('lue', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // Créer une notification (appelée en interne)
    public static function creer(
        int $userId, string $message, string $type) {
        Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'type'    => $type,
            'lue'     => false,
        ]);
    }
}
