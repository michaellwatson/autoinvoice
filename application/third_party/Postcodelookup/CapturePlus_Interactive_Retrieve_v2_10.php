<?php
class CapturePlus_Interactive_Retrieve_v2_10
{

   //Credit: Thanks to Stuart Sillitoe (http://stu.so/me) for the original PHP that these samples are based on.

   private $Key; //The key to use to authenticate to the service.
   private $Id; //The Id from a Find method to retrieve the details for.
   private $Data; //Holds the results of the query

   function CapturePlus_Interactive_Retrieve_v2_10($Key, $Id)
   {
      $this->Key = $Key;
      $this->Id = $Id;
   }

   function MakeRequest()
   {
      $url = "http://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Retrieve/v2.10/xmla.ws?";
      $url .= "&Key=" . urlencode($this->Key);
      $url .= "&Id=" . urlencode($this->Id);

      //Make the request to Postcode Anywhere and parse the XML returned
      //$file = simplexml_load_file($url);
      $file = $this->loadXML($url);
      //var_dump($file);

      //Check for an error, if there is one then throw an exception
      if ($file->Columns->Column->attributes()->Name == "Error") 
      {
         throw new Exception("[ID] " . $file->Rows->Row->attributes()->Error . " [DESCRIPTION] " . $file->Rows->Row->attributes()->Description . " [CAUSE] " . $file->Rows->Row->attributes()->Cause . " [RESOLUTION] " . $file->Rows->Row->attributes()->Resolution);
      }

      //Copy the data
      if ( !empty($file->Rows) )
      {
         foreach ($file->Rows->Row as $item)
         {
             $this->Data[] = array('Id'=>$item->attributes()->Id,'DomesticId'=>$item->attributes()->DomesticId,'Language'=>$item->attributes()->Language,'LanguageAlternatives'=>$item->attributes()->LanguageAlternatives,'Department'=>$item->attributes()->Department,'Company'=>$item->attributes()->Company,'SubBuilding'=>$item->attributes()->SubBuilding,'BuildingNumber'=>$item->attributes()->BuildingNumber,'BuildingName'=>$item->attributes()->BuildingName,'SecondaryStreet'=>$item->attributes()->SecondaryStreet,'Street'=>$item->attributes()->Street,'Block'=>$item->attributes()->Block,'Neighbourhood'=>$item->attributes()->Neighbourhood,'District'=>$item->attributes()->District,'City'=>$item->attributes()->City,'Line1'=>$item->attributes()->Line1,'Line2'=>$item->attributes()->Line2,'Line3'=>$item->attributes()->Line3,'Line4'=>$item->attributes()->Line4,'Line5'=>$item->attributes()->Line5,'AdminAreaName'=>$item->attributes()->AdminAreaName,'AdminAreaCode'=>$item->attributes()->AdminAreaCode,'Province'=>$item->attributes()->Province,'ProvinceName'=>$item->attributes()->ProvinceName,'ProvinceCode'=>$item->attributes()->ProvinceCode,'PostalCode'=>$item->attributes()->PostalCode,'CountryName'=>$item->attributes()->CountryName,'CountryIso2'=>$item->attributes()->CountryIso2,'CountryIso3'=>$item->attributes()->CountryIso3,'CountryIsoNumber'=>$item->attributes()->CountryIsoNumber,'SortingNumber1'=>$item->attributes()->SortingNumber1,'SortingNumber2'=>$item->attributes()->SortingNumber2,'Barcode'=>$item->attributes()->Barcode,'POBoxNumber'=>$item->attributes()->POBoxNumber,'Label'=>$item->attributes()->Label,'Type'=>$item->attributes()->Type,'DataLevel'=>$item->attributes()->DataLevel);
         }
      }
   }

   function HasData()
   {
      if ( !empty($this->Data) )
      {
         return $this->Data;
      }
      return false;
   }

   public function loadXML($url) {
    if (ini_get('allow_url_fopen') == true) {
      return $this->load_fopen($url);
    } else if (function_exists('curl_init')) {
      return $this->load_curl($url);
    } else {
      // Enable 'allow_url_fopen' or install cURL.
      throw new Exception("Can't load data.");
    }
  }
 
  private function load_fopen($url) {
    return simplexml_load_file($url);
  }
 
  private function load_curl($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return simplexml_load_string($result);
  }

}

//Example usage
//-------------
/*
$pa = new CapturePlus_Interactive_Retrieve_v2_10 ("PT11-BY93-CX13-ZU44","GBR|2101");
$pa->MakeRequest();
if ($pa->HasData())
{
   $data = $pa->HasData();
   foreach ($data as $item)
   {
      echo $item["Id"] . " 1<br/>";
      echo $item["DomesticId"] . " 2<br/>";
      echo $item["Language"] . " 3<br/>";
      echo $item["LanguageAlternatives"] . " 4<br/>";
      echo $item["Department"] . " 5<br/>";
      echo $item["Company"] . " 6<br/>";
      echo $item["SubBuilding"] . " 7<br/>";
      echo $item["BuildingNumber"] . " 8<br/>";
      echo $item["BuildingName"] . " 9<br/>";
      echo $item["SecondaryStreet"] . " 10<br/>";
      echo $item["Street"] . " 11<br/>";
      echo $item["Block"] . " 12<br/>";
      echo $item["Neighbourhood"] . " 13<br/>";
      echo $item["District"] . " 14<br/>";
      echo $item["City"] . " 15<br/>";
      echo $item["Line1"] . " 16<br/>";
      echo $item["Line2"] . " 17<br/>";
      echo $item["Line3"] . " 18<br/>";
      echo $item["Line4"] . " 19<br/>";
      echo $item["Line5"] . " 20<br/>";
      echo $item["AdminAreaName"] . " 21<br/>";
      echo $item["AdminAreaCode"] . " 22<br/>";
      echo $item["Province"] . " 23<br/>";
      echo $item["ProvinceName"] . " 24<br/>";
      echo $item["ProvinceCode"] . " 25<br/>";
      echo $item["PostalCode"] . " 26<br/>";
      echo $item["CountryName"] . " 27<br/>";
      echo $item["CountryIso2"] . " 28<br/>";
      echo $item["CountryIso3"] . " 29<br/>";
      echo $item["CountryIsoNumber"] . " 30<br/>";
      echo $item["SortingNumber1"] . " 31<br/>";
      echo $item["SortingNumber2"] . " 32<br/>";
      echo $item["Barcode"] . " 33<br/>";
      echo $item["POBoxNumber"] . " 34<br/>";
      echo $item["Label"] . " 35<br/>";
      echo $item["Type"] . " 36<br/>";
      echo $item["DataLevel"] . " 37<br/>";
   }
}
*/
?>