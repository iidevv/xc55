!function(e){e.fn.validationEngineLanguage=function(){},e.validationEngineLanguage={newLang:function(){e.validationEngineLanguage.allRules={required:{regex:"none",alertText:"* Υποχρεωτικό πεδίο",alertTextCheckboxMultiple:"* Παρακαλώ επιλέξτε",alertTextCheckboxe:"* Υποχρεωτικό πεδίο",alertTextDateRange:"* Και τα δύο πεδία ημ/νίας είναι υποχρεωτικά"},requiredInFunction:{func:function(e,a,t,F){return"test"==e.val()},alertText:"* Το πεδίο πρέπει να έχει την τιμή test"},dateRange:{regex:"none",alertText:"* Μη έγκυρη τιμή ",alertText2:"χρονικού διαστήματος"},dateTimeRange:{regex:"none",alertText:"* Μη έγκυρη τιμή ",alertText2:"χρονικού διαστήματος"},minSize:{regex:"none",alertText:"* Ελάχιστο μήκος ",alertText2:" χαρακτήρες"},maxSize:{regex:"none",alertText:"* Μέγιστο μήκος ",alertText2:" χαρακτήρες"},groupRequired:{regex:"none",alertText:"* Πρέπει να επιλέξετε τουλάχιστον μια τιμή"},min:{regex:"none",alertText:"* Η ελάχιστη τιμή είναι "},max:{regex:"none",alertText:"* Η μέγιστη τιμή είναι "},past:{regex:"none",alertText:"* Η ημ/νια πρέπει να είναι μικρότερη από "},future:{regex:"none",alertText:"* Η ημ/νια πρέπει να είναι μεγαλύτερη από "},maxCheckbox:{regex:"none",alertText:"* Μέγιστος αριθμός επιλογών ",alertText2:""},minCheckbox:{regex:"none",alertText:"* Παρακαλώ διαλέξτε τουλάχιστον ",alertText2:" επιλογή"},equals:{regex:"none",alertText:"* Τα πεδία δεν είναι όμοια"},creditCard:{regex:"none",alertText:"* Μη αποδεκτή πιστωτική κάρτα"},phone:{regex:/^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/,alertText:"* Μη έγκυρος αριθμός τηλεφώνου"},email:{regex:/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,alertText:"* Μη έγκυρο email"},integer:{regex:/^[\-\+]?\d+$/,alertText:"* Μη έγκυρος ακέραιος"},number:{regex:/^[\-\+]?((([0-9]{1,3})([\.][0-9]{3})*)|([0-9]+))?([,]([0-9]+))?$/,alertText:"* Μη έγκυρος δεκαδικός"},date:{func:function(e){var a=new RegExp(/^(\d{4})[\/\-\.](0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])$/).exec(e.val());if(null==a)return!1;var t=a[1],F=+a[2],d=+a[3],r=new Date(t,F-1,d);return r.getFullYear()==t&&r.getMonth()==F-1&&r.getDate()==d},alertText:"* Μη έγκυρη μορφή ημ/νίας (YYYY-MM-DD)"},ipv4:{regex:/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,alertText:"* Μη έγκυρη διεύθυνση IP"},ip:{regex:/((^\s*((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$))/,alertText:"* Μη έγκυρη διεύθυνση IP"},url:{regex:/^((https?|ftp):\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,alertText:"* Μη έγκυρη διεύθυνση URL"},onlyNumberSp:{regex:/^[0-9\ ]+$/,alertText:"* Επιτρέπονται μόνο αριθμοί"},onlyLetterSp:{regex:/^[a-zA-Zα-ωΑ-ΩάέήίόύώΆΈΉΊΌΎΏϊϋΪΫΐΰ\ \']+$/,alertText:"* Επιτρέπονται μόνο γράμματα"},onlyLetterNumber:{regex:/^[0-9a-zA-Zα-ωΑ-ΩάέήίόύώΆΈΉΊΌΎΏϊϋΪΫΐΰ]+$/,alertText:"* Επιτρέπονται μόνο γράμματα και αριθμοί"},ajaxUserCall:{url:"ajaxValidateFieldUser",extraData:"name=eric",alertText:"* This user is already taken",alertTextLoad:"* Validating, please wait"},ajaxUserCallPhp:{url:"phpajax/ajaxValidateFieldUser.php",extraData:"name=eric",alertTextOk:"* This username is available",alertText:"* This user is already taken",alertTextLoad:"* Validating, please wait"},ajaxNameCall:{url:"ajaxValidateFieldName",alertText:"* This name is already taken",alertTextOk:"* This name is available",alertTextLoad:"* Validating, please wait"},ajaxNameCallPhp:{url:"phpajax/ajaxValidateFieldName.php",alertText:"* This name is already taken",alertTextLoad:"* Validating, please wait"},validate2fields:{alertText:"* Please input HELLO"},dateFormat:{regex:/^(((0[1-9]|[12][0-9]|3[01])\/(0[13578]|10|12)\/(\d{4}))|(([0][1-9]|[12][0-9]|30)\/(0[469]|11)\/(\d{4}))|((0[1-9]|1[0-9]|2[0-8])\/(02)\/(\d{4}))|((29)\/(02)\/([02468][048]00))|((29)\/(02)\/([13579][26]00))|((29)\/(02)\/([0-9][0-9][0][48]))|((29)\/(02)\/([0-9][0-9][2468][048]))|((29)\/(02)\/([0-9][0-9][13579][26])))$/,alertText:"* Μη έγκυρη ημ/νία"},dateTimeFormat:{regex:/^(((0[1-9]|[12][0-9]|3[01])\/(0[13578]|10|12)\/(\d{4}))|(([0][1-9]|[12][0-9]|30)\/(0[469]|11)\/(\d{4}))|((0[1-9]|1[0-9]|2[0-8])\/(02)\/(\d{4}))|((29)\/(02)\/([02468][048]00))|((29)\/(02)\/([13579][26]00))|((29)\/(02)\/([0-9][0-9][0][48]))|((29)\/(02)\/([0-9][0-9][2468][048]))|((29)\/(02)\/([0-9][0-9][13579][26])))\s+((([0]?[1-9]|1[0-2])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])( )?(AM|am|aM|Am|PM|pm|pM|Pm))|(([0]?[0-9]|1[0-9]|2[0-3])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])))$/,alertText:"* Μη έγκυρη ημ/νία ή/και ώρα",alertText2:"Expected Format: ",alertText3:"dd/mm/yyyy hh:mm:ss AM|PM or ",alertText4:"dd/mm/yyyy HH:mm:ss "}}}},e.validationEngineLanguage.newLang()}(jQuery);