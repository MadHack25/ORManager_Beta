Ordering Registrator Beta Version , After which it became Ordering Manager

.Backend: Laravel 6.2
.FrontEnd: VueJS 2

* Web Application saves information of Ordered items from web (name , url , weight, price ect...) by User. 
  Each Order Information has to be enterred manually.
* Thanks to Vuejs2 Javascript framework it Updates Records Instantly. Using VueGoodTable Datatable - VueJS Package
  All records are pre-organized with Javascript while they are Actually saved in Database.
* User-Friendly interface. Every Edit/Delete/Update is done without Whole Page Refresh
* IP Filter Middleware helps user not To Login Each time on Application and stay Authorised on Custom IP
* Backup DB function - for backing up Database (security)
* 2 Modes: IP Auth & Public
  IP Auth Mode Allows user only if hes IP is same as Authorisation IP written in .env file
  Public Mode  -  Access From Every Location
* Custom Artisan Command for Switching Between Modes

This web application is Made for accounting Packages from internet (tools, computer parts, ect...)
For information Fast access and Mobility

GE ==============

Ordering Registrator ბეტა ვერსია , რომლის შემდეგაც ის გახდა Ordering Manager

.Backend: Laravel 6.2
.FrontEnd: VueJS 2


* ვებ-აპლიკაცია ინახავს გამოწერილ ამანათზე ინფორმაციას და ამუშავებს მას. 
ხდება Live რეჟიმში ინფორმაციის განახლება, დამატება, წაშლა და მათ შორის External საიტზე ავტორიზაცია Curl-ის საშუალებით.
ვიყენებ FastSimpleHTMLDom\Document ფექიჯს, რომლის გამოყენებითაც ვახერხებ სხვა საიტიდან ინფორმაციის წამოღებას html ფარსინგით.
რადგან გარკვეული url მისამართები მოითხოვს ავტორიზაციას, კონტროლერიდან DOMcontroller.php ვახდენ ავტორიზაციას External საიტზე და შემდეგ ვახდენ ინფორმაციის წამოღებას.
External Website - inex.ge ამანათების ჩამომტანი კომპანია. მას არ აქვს API ბექპოინტები, რის გამოც კონტროლერით ვახდენ html დომის ფარსინგს.
ძირითადი პლიუსები, რითაც ის განსხვავდება ეს ვებ-აპლიკაცია ordering.madstudio.ge-სგან:::

1. დაწერილია Laravel 6.2-ზე
2. გამოყენებული მაქვს VUE.JS Front-End Framework
3. საიტის დარეფრეშების გარეშე ხდება: inex.ge-ზე ავტორიზაცია, სასურველი ამანათის # თრექინგ ნომერით # ძიება , შემდგომ მისი - ტრანსპორტირების თანხის წამოღება და მონაცემთა ბაზაში შენახვა + ვალუტის კონვერტაცია
ვალუტის წამოსაღებად გამოვიყენე new SoapClient('http://nbg.gov.ge/currency.wsdl')
4. VueGoodTable Datatable - გამოყენებულია საკმაოდ მოქნილი Datatable npm ფექიჯი VUE.JS-ისთვის, რომლის საშუალებითაც ხდება ინფორმაციის რეალურ დროში განახლება
5. საიტი იტვირთება გაცილებით სწრაფად, ვიდრე ordering.madstudio.ge
(რადგან არ მაქვს Laravel-ის ადმინ ფექიჯი - Voyager) - ამ ვებ-აპლიკაციას არ აქვს "ადმინკა" ჯერ-ჯერობით. აქცენტი გაკეთებულია VUE.JS DataTable-ზე და მასთან დაკავშირებულ ფუნქციებზე.


