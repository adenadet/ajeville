// src/globalMethods.js
import moment from 'moment';

export const globalMethods = {
    methods: {
        addOne(value) {
            if (isNaN(value)) {
                return '0';
            }
            let val = value + 1;
            return val;
        },
        age(value) {
            return moment().diff(moment(value, "DD MMM YYYY"), 'years');
        },
        capitalize(text) {
            if(text == null){return '';}
            return text.toUpperCase();
        },
        className(text){
            if (text == null || text.length === 0) {
                return '';
            }
            const parts = text.split('\\');   // split by backslash
            const className = parts.pop();   // last part
            return className;
        },
        createEmptyConsultation(id = null) {
            return {
                id,
                complaint: '',
                history: '',
                initial_icd_10: [],
                final_icd_10: [],
                plan: {
                    plan: '',
                    non_drug: '',
                    follow_up_date: null,
                    follow_up_note: '',
                    intent: {
                        admission: false,
                        referral: false,
                    },
                },
                requests: {
                    prescription: [],
                    laboratory: [],
                    radiology: [],
                    physiotherapy: [],
                    dialysis: {},
                    admission: null,
                    referral: null,
                },
            }
        },
        currency(value) {
            if (isNaN(value)) {
                return '₦ 0.00';
            }
            let val = (value / 1).toFixed(2).replace(',', '.');
            return  '₦ '+val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        dateCompareToday(date, query){
            var test_date = new Date(date);
            
            var today = new Date();
            today.setHours(0,0,0,0);
            if (query == '='){return (test_date == today);}
            else if (query == '<'){return (test_date < today);}
            else if (query == '<='){return (test_date <= today);}
            else if (query == '>'){return (test_date > today);}
            else if (query == '>='){return (test_date >= today);}
        },
        dateDay(text) {
            return moment(text).format('DD');
        },
        dateMonth(text) {
            return moment(text).format('MM');
        },
        dateYear(text) {
            return moment(text).format('YYYY');
        },
        ExcelDate(text) {
            return moment(text).format('Do MMMM, YYYY');
        },
        ExcelDateShort(text) {
            return moment(text).format('DD/MM/YYYY');
        },
        ExcelDateMonth(text) {
            return moment(text).format('MMM Do');
        },
        Excel6Months(text) {
            return moment(text).add(6, 'M').format('MMM Do, YYYY');
        },
        ExcelMonthYear(text) {
            return moment(text).format('MMM, YYYY');
        },
        ExcelTime(text) {
            return moment(text).format('hh:mm:ss A');
        },
        FullName(text) {
            if (text == null) {
                return 'Old User/Staff';
            }
            return text.last_name + ', ' + text.first_name + (text.middle_name != null ? ' ' + text.middle_name : '');
        },
        FullDate(text) {
            return moment(text).format('LLLL');
        },
        firstUp(text) {
            if (text == null || text.length === 0) {
                return '';
            }
            return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
        },
        getAge(text) {
            var birthYear = parseInt(moment(text).format('YYYY'));
            var currentDate = new Date();
            var currentYear = currentDate.getFullYear();
            var age = currentYear - birthYear;
            return age + ' years';
        },
        Names(text) {
            if (text == null) {
                return 'Old User/Staff';
            }
            return text.first_name + (text.middle_name != null ? ' ' + text.middle_name : '');
        },
        patientAddress(text){
            if ((text == null) || (text.user == null)) {
                return 'No Patient Found';
            }
            
            return text.user.address + ', ' + text.user.city + ', ' + text.user.state + ', ' + text.user.country;
        },
        patientName(text) {
            if ((text == null) || (text.user == null)) {
                return 'Invalid Patient ID';
            }
            return text.user.last_name + ', ' + text.user.first_name + (text.user.middle_name != null ? ' ' + text.user.middle_name : '')+ ' ['+text.unique_id+']';
        },
        profilePicture(text) {
            if (text == null) {
                return '/img/profile/default.png';
            } else {
                return '/img/profile/' + text;
            }
        },
        readMore(text, length, suffix) {
            if (text == null) {
                return text;
            } else if (text.length <= length) {
                return text;
            } else {
                return text.substring(0, length) + suffix;
            }
        },
        shortDate(text) {
            return moment(text).format('MMM Do, YY');
        },
        timeDifference(start, end, format){
            var timeBegin = moment(start);
            var timeEnd = moment(end);

            return timeEnd.diff(timeBegin, format)+' '+format;
        },
        treatFont(text) {
            let story = text.replaceAll("font-size: 1rem", "font-size: 2rem");
            return story;
        },
    },
};
