!function(a){a.fn.validationEngineLanguage=function(){},a.validationEngineLanguage={newLang:function(){a.validationEngineLanguage.allRules={required:{regex:"none",alertText:"* Bidang ini wajib",alertTextCheckboxMultiple:"* Silahkan pilih pilihan",alertTextCheckboxe:"* Kotak centang ini wajib",alertTextDateRange:"* Kedua bidang rentang tanggal ini wajib"},requiredInFunction:{func:function(a,e,d,t){return"test"==a.val()},alertText:"* Bidang harus sama dengan uji"},dateRange:{regex:"none",alertText:"* Rentang Tanggal ",alertText2:"Tidak Sah"},dateTimeRange:{regex:"none",alertText:"* Rentang Tanggal Waktu ",alertText2:"Tidak Sah"},minSize:{regex:"none",alertText:"* Minimum ",alertText2:" karakter yang dibutuhkan"},maxSize:{regex:"none",alertText:"* Maksimum ",alertText2:" karakter yang dibutuhkan"},groupRequired:{regex:"none",alertText:"* Anda harus mengisi salah satu bidang berikut"},min:{regex:"none",alertText:"* Nilai minimalnya adalah "},max:{regex:"none",alertText:"* Nilai maksimum adalah "},past:{regex:"none",alertText:"* Tanggal sebelum "},future:{regex:"none",alertText:"* Tanggal sesudah "},maxCheckbox:{regex:"none",alertText:"* Maksimum ",alertText2:" pilihan yang diperbolehkan"},minCheckbox:{regex:"none",alertText:"* Silahkan pilih ",alertText2:" pilihan"},equals:{regex:"none",alertText:"* Bidang tidak cocok"},creditCard:{regex:"none",alertText:"* Nomor kartu kredit tidak sah"},phone:{regex:/^([\+][0-9]{1,3}([ \.\-])?)?([\(][0-9]{1,6}[\)])?([0-9 \.\-]{1,32})(([A-Za-z \:]{1,11})?[0-9]{1,4}?)$/,alertText:"* Nomor telepon tidak sah"},email:{regex:/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,alertText:"* Alamat email tidak sah"},integer:{regex:/^[\-\+]?\d+$/,alertText:"* Bukan bilangan bulat yang sah"},number:{regex:/^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,alertText:"* Bukan angka desimal mengambang yang sah"},date:{func:function(a){var e=new RegExp(/^(\d{4})[\/\-\.](0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])$/).exec(a.val());if(null==e)return!1;var d=e[1],t=+e[2],n=+e[3],F=new Date(d,t-1,n);return F.getFullYear()==d&&F.getMonth()==t-1&&F.getDate()==n},alertText:"* Tanggal tidak sah, harus dalam format TTTT-BB-HH"},ipv4:{regex:/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,alertText:"* Alamat IP tidak sah"},ip:{regex:/((^\s*((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$))/,alertText:"* Alamat IP tidak sah"},url:{regex:/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,alertText:"* URL tidak sah"},onlyNumberSp:{regex:/^[0-9\ ]+$/,alertText:"* Angka saja"},onlyLetterSp:{regex:/^[a-zA-Z\ \']+$/,alertText:"* Huruf saja"},onlyLetterAccentSp:{regex:/^[a-z\u00C0-\u017F\ ]+$/i,alertText:"* Huruf saja"},onlyLetterNumber:{regex:/^[0-9a-zA-Z]+$/,alertText:"* Karakter khusus tidak diperbolehkan"},ajaxUserCall:{url:"ajaxValidateFieldUser",extraData:"name=eric",alertText:"* Pengguna ini sudah diambil",alertTextLoad:"* Mengesahkan, silahkan tunggu"},ajaxUserCallPhp:{url:"phpajax/ajaxValidateFieldUser.php",extraData:"name=eric",alertTextOk:"* Nama pengguna ini tersedia",alertText:"* Pengguna ini sudah diambil",alertTextLoad:"* Mengesahkan, silahkan tunggu"},ajaxNameCall:{url:"ajaxValidateFieldName",alertText:"* Nama ini sudah diambil",alertTextOk:"* Nama ini tersedia",alertTextLoad:"* Mengesahkan, silahkan tunggu"},ajaxNameCallPhp:{url:"phpajax/ajaxValidateFieldName.php",alertText:"* Nama ini sudah diambil",alertTextLoad:"* Mengesahkan, silahkan tunggu"},validate2fields:{alertText:"* Silakan masukan HELLO"},dateFormat:{regex:/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,alertText:"* Tanggal Tidak Sah"},dateTimeFormat:{regex:/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+([01]?[0-9]|2[0-3]){1}:(0?[1-5]|[0-5][0-9]){1}:(0?[0-6]|[0-5][0-9]){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+([01]?[0-9]|2[0-3]){1}:(0?[1-5]|[0-5][0-9]){1}:(0?[0-9]|[0-5][0-9]){1})$/,alertText:"* Tanggal atau Format Tanggal Tidak Sah",alertText2:"Format yang diharapkan: ",alertText3:"bb/hh/tttt jj:mm:dd atau ",alertText4:"tttt-bb-hh jj:mm:dd"}}}},a.validationEngineLanguage.newLang()}(jQuery);