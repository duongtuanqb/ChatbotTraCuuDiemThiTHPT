<?php 

include ('./lib/Document.php');
include ('./lib/Element.php');
include ('./lib/Encoder.php');
include ('./lib/Errors.php');
include ('./lib/Query.php');
include ('./lib/Chatfuel.php');

$chatfuel = new Chatfuel(TRUE);

$html = getUser($_GET["sbd"]);

if($html) {

	$getUser = (new Document($html))->find('.width_name a')[0]->getAttribute('href');

	$getName = (new Document(getDiem($getUser)))->find('.name_thisinh')[0];
	$getDiem = (new Document(getDiem($getUser)))->find('.monthi_thisinh ul li');

	$chatfuel->sendText(👤.strip_tags($getName));

	$ketqua = '';
	foreach ($getDiem as $diem) {

		$ketqua .= preg_replace("/[\\n\\r]+/", "💯 ", strip_tags($diem), 1);
	}
	$chatfuel->sendText($ketqua);

} else {
	$chatfuel->sendText("Không tìm thấy kết quả, bạn hãy nhập đúng số báo danh 🙏");
}

function getUser($q) {
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
	curl_setopt($ch, CURLOPT_URL, 'http://diemthi.vnexpress.net/index/result?q='. $q .'&college=31&area=2');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);

	$resurt = curl_exec($ch);

	curl_close($ch);

	$data = json_decode($resurt, true)['data'];

	if($data == 'Không tìm thấy kết quả') return false;

	return $data;
}

function getDiem($url) {
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
	curl_setopt($ch, CURLOPT_URL, 'http://diemthi.vnexpress.net'.$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);

	$resurt = curl_exec($ch);

	curl_close($ch);

	return $resurt;
}