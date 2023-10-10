## Các chức năng đã làm được:
1. Tạo 1 form gồm các trường: To, title và Content
2. Khi nhấn submit thì 1 tài khoản khác được nhập trong trường 'To' sẽ nhận được thông báo là nội dung của trường 'Content' ngay lập tức
3. Hiển thị nhãn thông báo khi tài khoản nhận được thông báo.
4. Click chuột để  đánh dấu là đã đọc thông báo
## Các bước thực hiện:
1. Khởi tạo project:
- composer create-project --prefer-dist laravel/laravel NotificationRealTime
2. Chỉnh sửa file .env để kết nối database
3. Tạo bảng notification để lưu thông báo:
- php artisan notifications:table
- php artisan migrate
4. tạo file TestNotification:
- php artisan make:notification TestNotification
5. function via($notifiable) để định nghĩa phương thức thông báo. Có các phương thức như: mail, database, broadcast, vonage, và slack. Ở đây mình dùng database tức là chỉ lưu data của thông báo vào db.
6. Sử dụng function toArray để event trả ra một data lưu vào bảng notifications. (hoặc toDatabase() )
7. Make Auth - login, register, logout:
- composer require laravel/ui
- php artisan ui vue --auth
- php artisan migrate
8. Tạo form nhập dữ liệu cho thông báo gửi đi: notificaton.blade.php sau đó include vào home.blade.php
9. Tạo controller cho việc gửi notification:
- php artisan make:controller SendNotification
10. Thêm route trong file routes\web.php
11. Thêm tab notification vào layouts app.blade.php
12. Sử dụng Pusher để thông báo real-time:
- composer require pusher/pusher-php-server
13. Đăng kí tài khoản và tạo app trên pusher.com -> chon BE tech = PHP
14. sửa file .env:
- BROADCAST_DRIVER=pusher

- PUSHER_APP_ID=xxxxx
- PUSHER_APP_KEY=xxxxxxxxxxxxxxxxxxxxx
- PUSHER_APP_SECRET=xxxxxxxxxxxxxxxxxxxxx
- PUSHER_APP_CLUSTER=ap1
- Thay lần lượt các cái trên bằng các key tương ứng khi tạo xong app trên pusher.com
15. Vào config/app.php Loại bỏ comment ở BroadcastServiceProvider
- App\Providers\BroadcastServiceProvider::class
16. Tạo event gửi notification bằng câu lệnh:
- php artisan make:event NotificationEvent
- trong hàm broadcastOn(), ta truyền đối số $chanel vào PrivateChannel($chanel). $chanel là id của người nhận. Ý tưởng là chỉ gửi thông báo cho riêng người dùng đó chứ không gửi đồng loạt cho tất cả.
17. Trong controller ta use Pusher, đồng thời truyền param vào hàm trigger như sau:
- $pusher->trigger('NotificationEvent', $request['noti_to'], $data);
- Biến $request['noti_to'] chính là đích đến của thông báo (hay nói cách khác là id của người dùng sẽ nhận được thông báo).
18. Thêm script vào app.blade.php để bắt sự kiện nhận thông báo. tham khảo tại: https://pusher.com/docs/channels/using_channels/events/
19. Chú ý: không gọi hàm env() ở bất cứ đâu ngoài các files config. Muốn gọi các hằng của env thì phải dùng Config::get() (tham khảo trong controller)
## Tài liệu tham khảo:
- https://viblo.asia/p/luu-va-gui-notification-real-time-trong-laravel-OeVKB3x0ZkW
- https://pusher.com/docs/channels/using_channels/events/
- https://laravel.com/docs/8.x/notifications
