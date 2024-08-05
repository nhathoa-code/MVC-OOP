<?php

use NhatHoa\Framework\Core\View;
use NhatHoa\Framework\Facades\Auth;
use NhatHoa\Framework\Registry;

function view($file_path,$data = array())
{   
    $view = new View($file_path);
    $view->setData($data);
    $view->render();
}

function baseUrl()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $baseUrl = $protocol . $_SERVER['HTTP_HOST'];
    $baseUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    return $baseUrl;
}

function url($path)
{
    $baseUrl = baseUrl();
    return $baseUrl . trim($path,"/");
}

function redirect($path)
{
    $redirected_url = url($path);
    header("Location: {$redirected_url}");
    exit;
}

function redirect_to($url)
{
    header("Location: $url");
    exit;
}

function redirect_back()
{
    $previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    if($previousPage){
        header('Location: ' . $previousPage);
        exit;
    }
}

function back_url()
{
    return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
}

function current_route() {
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($url);
    $url_without_params = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'];
    return trim(str_replace(baseUrl(), "", $url_without_params),"/");
}

function current_url()
{
    $currentURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $currentURL;
}

function get_query_string($url)
{
    $queryString = parse_url($url, PHP_URL_QUERY);
    return $queryString;
}

function response()
{
    return Registry::get("response");
}

function slug($string)
{
    $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);

    $string = strtolower(str_replace(' ', '-', $string));

    $string = preg_replace('/-+/', '-', $string);

    return $string;
}

function array_print($data)
{
    if(is_array($data) || is_object($data)){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

function delete_file($file_path)
{
    $file_path = PUBLIC_PATH . "/" . $file_path;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

function rename_file($old_name,$new_name)
{
    $old_name = PUBLIC_PATH . "/" . $old_name;
    $new_name = PUBLIC_PATH . "/" . $new_name;
    if(file_exists($old_name)){
        rename($old_name,$new_name);
    }
}

function remove_dir($dir)
{
    $dir = PUBLIC_PATH . "/" . $dir;
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($dir);
}

function getFiles($path_to_directory)
{
    $files = scandir(PUBLIC_PATH . "/" . $path_to_directory);
    natsort($files);
    return array_map(function($item) use($path_to_directory){
        return url($path_to_directory . "/" . $item);
    },array_values(array_filter($files,function($item){
        return !in_array($item, array('.', '..'));
    })));
}

function query($name)
{
    return isset($_GET[$name]) ? $_GET[$name] : null;
}

function get_query($key)
{
    return isset($_GET[$key]) ? $_GET[$key] : null;
}

function generateToken($length = 32)
{
    $randomBytes = random_bytes($length);
    $token = bin2hex($randomBytes);
    return $token;
}

function now()
{
    return (new DateTime())->format('Y-m-d H:i:s');
}

function login($guard = "user")
{
    return Auth::check($guard);
}

function getUser($guard = 'user')
{
    return Auth::user($guard);
}

function array_find(array $arr,callable $func)
{
    for($i=0; $i < count($arr); $i++)
    {
        if($func($arr[$i],$i))
        {
            return $arr[$i];
        }
    }
    return null;
}

function csrf_token()
{
    return isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : null;
}

function isMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $mobileKeywords = array('Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone');
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

function pagination($totalPages,$currentPage,$maxPagesToShow = 5)
{
    $queryParameters = $_GET;
    unset($queryParameters['page']);
    unset($queryParameters['url']);

    $pagination = '<nav aria-label="Page navigation example">';
    $pagination .= '<ul class="pagination">';

    // Previous button
    if ($currentPage > 1) {
        $queryParameters['page'] = $currentPage - 1;
        $pagination .= '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParameters) . '">Previous</a></li>';
    }

    $halfMaxPagesToShow = floor($maxPagesToShow / 2);
    $startPage = max(1, $currentPage - $halfMaxPagesToShow);
    $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

    // Display ellipsis if necessary
    if ($startPage > 1) {
        $queryParameters['page'] = 1;
        $pagination .= '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParameters) . '">1</a></li>';
        if ($startPage > 2) {
            $pagination .= '<li class="page-item"><span>...</span></li>';
        }
    }

    // Page numbers
    for ($i = $startPage; $i <= $endPage; $i++) {
        $queryParameters['page'] = $i;
        $activeClass = ($i == $currentPage) ? 'active' : '';
        $pagination .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?' . http_build_query($queryParameters) . '">' . $i . '</a></li>';
    }

    // Display ellipsis if necessary
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $pagination .= '<li><span>...</span></li>';
        }
        $queryParameters['page'] = $totalPages;
        $pagination .= '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParameters) . '">' . $totalPages . '</a></li>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        $queryParameters['page'] = $currentPage + 1;
        $pagination .= '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParameters) . '">Next</a></li>';
    }

    $pagination .= '</ul>';

    return $pagination;
}

function abort($code = 401,$message = "Bạn không có quyền này",$headers = []){
    foreach ($headers as $header => $value) {
        header("$header: $value");
    }
    http_response_code($code);
    die($message);
}