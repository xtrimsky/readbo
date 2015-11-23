var Common = {
        /*
         * formatting date using timestamp
         */
        formatDate: function(timestamp, us_format){
            timestamp = parseFloat(timestamp) * 1000;

            var now = new Date();

            var date_obj = new Date(timestamp);
            var date_day = date_obj.getDate();
            var date_month = date_obj.getMonth() + 1;
            var date_hours = date_obj.getHours();
            var date_minutes = date_obj.getMinutes();
            var date_years = date_obj.getFullYear();

            var s_day = date_day.toString();
            var s_month = date_month.toString();
            var s_hours = date_hours.toString();
            var s_minutes = date_minutes.toString();

            var extra_string = '';
            if(us_format){
                extra_string = 'am';
                if(date_hours >= 12){
                    extra_string = 'pm';
                    date_hours -= 12;
                    s_hours = date_hours.toString();
                }

                if(date_hours == 0){
                    date_hours = 12;
                    s_hours = date_hours.toString();
                }
            }

            if(date_hours < 10){
                s_hours = '0' + s_hours;
            }

            if(date_minutes < 10){
                s_minutes = '0' + s_minutes;
            }

            //if date is today, donesnt show the date
            if(date_day === now.getDate() && date_month === (now.getMonth() + 1) && date_years === now.getFullYear()){
                return s_hours + ':' + s_minutes + extra_string;
            }

            if(date_day < 10){
                s_day = '0' + s_day;
            }

            if(date_month < 10){
                s_month = '0' + s_month;
            }

            if(now.getFullYear() !== date_years){
                return s_hours + ':' + s_minutes + extra_string + ' ' + s_month + '/' + s_day + '/' + date_years;
            }

            return s_hours + ':' + s_minutes + extra_string + ' ' + s_month + '/' + s_day;
        }
};