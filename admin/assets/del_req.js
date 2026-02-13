$(document).on("click", ".BtnDeleteRequest", (e) => {
    const [RequestID, TopicName] = $(e.target).val().split("_");
    Swal.fire({
        title: "ต้องการลบ",
        text: `${TopicName} ใช่หรือไม่?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "ใช่",
        cancelButtonText: "ยกเลิก",
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "backend/API/request.api.php",
                data: {
                    path: "/delete_request",
                    RequestID: RequestID
                },
                dataType: "JSON",
                success: (res) => {
                    if (res.status === 200) {
                        window.location.reload();
                    }
                },
                error: (err) => {
                    Swal.fire({
                        title: "system fail",
                        text: "กรุณาติดต่อผู้พัฒนา",
                        icon: "error"
                    });
                }
            });
        }
    });
});