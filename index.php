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
	$results = [];
	$getMon = (new Document($html))->find('.width_monhoc');
	$getDiem = (new Document($html))->find('.width_sbd');
	$getDiem = array_slice($getDiem, 3);

	foreach ($getMon as $key => $value) {
		$results[$value->text()] = $getDiem[$key+1]->text();
	}

	$getSDB = (new Document($html))->find('.width_sbd a')[0]->text();
	$ketqua = "Thí sinh SDB: $getSDB \n";

	foreach ($results as $mon => $diem) {
		if(!$diem) {
			continue;
		}
		$ketqua .= "💯 $mon: $diem \n"; 
	}

	$chatfuel->sendText($ketqua);

} else {
	$chatfuel->sendText("Không tìm thấy kết quả, vui lòng nhập đúng số báo danh 🙏");
}

function getUser($q) {
	$mavung = substr($q, 0, 2);

	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
	curl_setopt($ch, CURLOPT_URL, 'http://diemthi.vnexpress.net/index/result?q='. $q .'&college=' . $mavung . '&area=2');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	curl_exec($ch);

	$resurt = curl_exec($ch);

	curl_close($ch);

	$data = json_decode($resurt, true)["data"];

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
