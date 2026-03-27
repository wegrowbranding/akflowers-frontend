<?php

class NotificationController extends Controller {

    public function index(): void {
        requireAuth();
        $this->view('notifications.index');
    }

    public function send(): void {
        requireAuth();
        
        $title = $_POST['title'] ?? '';
        $message = $_POST['message'] ?? '';
        $imageUrl = $_POST['image_url'] ?? '';

        if (empty($title) || empty($message)) {
            $_SESSION['error'] = 'Title and Message are required.';
            $this->view('notifications.index', []);
            return;
        }

        $res = ApiClient::post('/admin/send-custom-notification', [
            'title' => $title,
            'message' => $message,
            'image_url' => $imageUrl,
            'type' => 'admin_custom'
        ]);

        if (isset($res['success']) && $res['success']) {
            $_SESSION['success'] = 'Broadcast notification sent successfully!';
        } else {
            $_SESSION['error'] = $res['message'] ?? 'Failed to send notification.';
        }

        $this->view('notifications.index', []);
    }
}
