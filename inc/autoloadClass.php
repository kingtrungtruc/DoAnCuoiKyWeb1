<?php 
    //Đặt giá trị của tùy chọn cấu hình đã cho. Tùy chọn cấu hình sẽ giữ giá trị mới này trong quá trình thực thi tập lệnh và sẽ được khôi phục ở cuối tập lệnh
    ini_set("display_errors", 1);
    //thiết lập chỉ thị error_reporting tại runtime. PHP có nhiều level của lỗi, sử dụng hàm này để thiết lập level đó tại runtime, E_ALL = Tất cả error và warning, ngoại trừ E_STRICT (E_STRICT sẽ là bộ phận của E_ALL như của PHP 6.0)
    error_reporting(E_ALL);

    function _autoloadClass($class){
        require_once $class . '.php';
    }

    //đăng ký hàm _autoload do mình tự định nghĩa thay thế cho hàm __autoload có sẵn của PHP
    spl_autoload_register('_autoloadClass');
?>