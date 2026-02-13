const loadCSS = (href) => {
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = href;
    document.head.appendChild(link); // เพิ่มเข้าไปใน `<head>`
};

const loadJS = (src, callback) => {
    const script = document.createElement("script");
    script.src = src;
    script.onload = () => {
        if (callback) callback(); // เรียก callback เมื่อโหลดเสร็จ
    };
    script.onerror = () => {
        console.error(`Failed to load script: ${src}`);
    };
    document.body.appendChild(script); // เพิ่มเข้าไปใน `<body>`
};

export const form_validation = (FORM_DOM) => {
    $(FORM_DOM).each((index, form) => {
        $(form).on('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(form).addClass('was-validated');
        });
    });
}

export const ajax = async (METHOD, URL, DATA, DATA_TYPE) => {
    try {
        const ajax_res = await $.ajax({
            type: METHOD,
            url: URL,
            data: DATA,
            dataType: DATA_TYPE,
        });
        return ajax_res
    } catch (err) {
        console.error("ajax()", err);
        throw new Error(`ajax() failed: ${err}`);
    }
}

export const data_table_en = (TABLE_DOM, ORDER_BY = "desc", Page_Length = 10) => {
    loadCSS("./assets/plugins/datatable/css/dataTables.bootstrap4.min.css")
    loadJS("./assets/plugins/datatable/js/jquery.dataTables.min.js", () => {
        $(TABLE_DOM).DataTable({
            order: [
                [0, ORDER_BY]
            ],
            pageLength: Page_Length
        });
    });
}

export const select2 = (SELECT_DOM, WidthStyle = $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style') => {
    loadCSS("./assets/plugins/select2/css/select2.min.css");
    loadCSS("./assets/plugins/select2/css/select2-bootstrap4.css");
    loadJS("./assets/plugins/select2/js/select2.min.js", () => {
        $(SELECT_DOM).select2({
            theme: 'bootstrap4',
            width: WidthStyle,
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
    })
}

export const my_select = (SELECT_DOM, WIDTH_CONTAINER, placeholder = "Select an option") => {
    const $select = $(SELECT_DOM);
    if ($select.length === 0) {
        console.error(`Element not found: ${SELECT_DOM}`);
        return;
    }

    // วนลูปสำหรับแต่ละ `<select>`
    $select.each(function () {
        const $originalSelect = $(this);
        const selectClasses = $select.attr('class');
        let width_setting = $originalSelect.outerWidth();
        if (WIDTH_CONTAINER != undefined && WIDTH_CONTAINER != "") {
            width_setting = WIDTH_CONTAINER
        }
        // ตรวจสอบว่า `<select>` มีค่า selected หรือไม่
        const selectedText = $originalSelect.find('option:selected').text();

        // สร้าง container สำหรับ custom dropdown
        const $container = $('<div>', { class: 'my-select2-container' }).css({
            position: 'relative',
            display: 'inline-block',
            width: width_setting,
        });

        // สร้างปุ่ม dropdown เลียนแบบ select
        const $dropdownToggle = $('<div>', {
            class: 'my-select2-toggle',
            text: selectedText || placeholder,
        })
        $dropdownToggle.addClass(selectClasses)

        // สร้าง dropdown สำหรับตัวเลือก
        const $dropdown = $('<div>', { class: 'my-select2-dropdown' }).css({
            position: 'absolute',
            top: '100%',
            left: 0,
            width: '100%',
            maxHeight: '200px',
            overflowY: 'auto',
            background: 'white',
            border: '1px solid #ccc',
            borderRadius: '4px',
            zIndex: 1000,
            display: 'none',
        });

        // สร้าง input สำหรับค้นหา
        const $searchInput = $('<input>', {
            type: 'text',
            class: 'my-select2-search',
            placeholder: 'Search...',
        });
        $searchInput.addClass("form-control form-control-sm")

        // เพิ่มตัวเลือกจาก `<select>` ลงใน dropdown
        $dropdown.append($searchInput);
        $originalSelect.find('option').each(function () {
            const value = $(this).val();
            const text = $(this).text();

            const $item = $('<div>', { class: 'my-select2-item', 'data-value': value, text }).css({
                padding: '8px',
                // cursor: 'pointer',
            });

            // กดเลือกแล้วอัปเดตค่าใน `<select>`
            $item.on('click', function () {
                $originalSelect.val(value).trigger('change'); // อัปเดตค่าใน `<select>` ดั้งเดิม
                $dropdownToggle.text(text); // แสดงค่าที่เลือกในปุ่ม
                $dropdown.hide(); // ปิด dropdown
            });

            $dropdown.append($item);
        });

        // เพิ่ม input และ dropdown ใน container
        // $dropdown.append($searchInput).append($dropdown);
        $container.append($dropdownToggle).append($dropdown);

        // ซ่อน `<select>` ดั้งเดิม
        $originalSelect.hide();

        // เพิ่ม container เข้าไปใน DOM
        $originalSelect.after($container);

        // แสดง/ซ่อน dropdown เมื่อคลิก
        $dropdownToggle.on('click', function () {
            $dropdown.toggle();
            $searchInput.focus();
        });

        // กรองตัวเลือกเมื่อพิมพ์
        $searchInput.on('input', function () {
            const query = $(this).val().toLowerCase();
            $dropdown.children('.my-select2-item').each(function () {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(query));
            });
        });

        // ปิด dropdown เมื่อคลิกข้างนอก
        $(document).on('click', function (e) {
            if (!$container.is(e.target) && $container.has(e.target).length === 0) {
                $dropdown.hide();
            }
        });
    });
};

export const select_date = (INPUT_DOM = ".SelectDate", FORMAT = "DD/MM/YYYY") => {
    // โหลด CSS
    loadCSS("./assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css");
    loadCSS("https://fonts.googleapis.com/icon?family=Material+Icons");

    // โหลด JS และใช้งาน moment.js กับ bootstrap-material-datetimepicker
    loadJS("./assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js", () => {
        loadJS("./assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js", () => {
            $(INPUT_DOM).bootstrapMaterialDatePicker({
                time: false,
                format: FORMAT,
            });
        });
    });
};

export const select_time = (INPUT_DOM = ".SelectTime", FORMAT = "HH:mm") => {
    // โหลด CSS
    loadCSS("./assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css");
    loadCSS("https://fonts.googleapis.com/icon?family=Material+Icons");

    // โหลด JS และใช้งาน moment.js กับ bootstrap-material-datetimepicker
    loadJS("./assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js", () => {
        loadJS("./assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js", () => {
            $(INPUT_DOM).bootstrapMaterialDatePicker({
                date: false,
                format: FORMAT,
            });
        });
    });
};

export const ui_append_option = (SELECT_DOM, data, KeyID, KeyName) => {
    let html = '';
    data.forEach(row => {
        html += `<option value="${row[KeyID]}">${row[KeyName]}</option>`;
    });
    $(SELECT_DOM).append(html)
}
export const flatpickr = (
    INPUT_DOM = ".SelectDate", 
    config = { // (config ที่รับมาจาก transaction.php)
        enableTime: false,
        dateFormat: "Y-m-d",
    }
) => {
    loadCSS("https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css");
    loadJS("https://cdn.jsdelivr.net/npm/flatpickr", () => {
        loadJS("https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js", () => {
            // ถ้า user ต้องการ พ.ศ. (thaiBuddhist: true) inject hook 'formatDate' เข้าไป
            if (config.locale === 'th' && config.thaiBuddhist === true && config.altInput === true) {
                
                // ระบุ 'window.flatpickr' เพื่ออ้างถึงไลบรารี
                const thaiMonths = window.flatpickr.l10ns.th.months.shorthand;
                const originalDateFormat = config.dateFormat; // (เช่น "d/m/Y")

                // สร้าง config ใหม่ โดย override formatDate
                config = Object.assign({}, config, {
                    formatDate: (dateObj, formatString) => {
                        
                        // ถ้าเป็น 'dateFormat' (ค่าจริง) ให้ส่งค่า ค.ศ. (d/m/Y) กลับไป
                        if (formatString === originalDateFormat) {
                            
                            // ต้องระบุ 'window.flatpickr'
                            return window.flatpickr.formatDate(dateObj, originalDateFormat);
                        }

                        // ถ้าเป็น 'altFormat' (ค่าแสดงผล) ให้ประกอบร่าง พ.ศ. เอง
                        const day = dateObj.getDate();
                        const month = thaiMonths[dateObj.getMonth()]; // e.g., "พ.ย."
                        const year = dateObj.getFullYear() + 543;     // e.g., 2568

                        // คืนค่า "j M Y" (พ.ศ.)
                        return `${day} ${month} ${year}`; // ผลลัพธ์: "13 พ.ย. 2568"
                    }
                });
            }
            
            // ใช้งาน flatpickr ด้วย config ที่อาจจะ ถูกแก้ไขแล้ว
            // (จุดนี้ถูกต้องแล้ว เพราะ .flatpickr() เป็น jQuery plugin)
            $(INPUT_DOM).flatpickr(config);
        })
    });
}