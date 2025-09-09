<?php
require_once __DIR__ . '/../model/FeedbackModel.php';

class FeedbackController {
    private FeedbackModel $model;

    public function __construct() {
        $this->model = new FeedbackModel();
    }
    public function list(): array {
        return $this->model->list(20, 0);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rate = (int)($_POST['rating'] ?? 0);
            $message = trim($_POST['message'] ?? '');

            if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $rate >= 1 && $rate <= 5 && $message) {
                $this->model->create($name, $email, $rate, $message);
                header("Location: /index.php"); // redirect back to list
                exit;
            } else {
                $error = "All fields are required and rating must be 1-5.";
                require __DIR__ . '../web-page.php';
            }
        }
    }
}
