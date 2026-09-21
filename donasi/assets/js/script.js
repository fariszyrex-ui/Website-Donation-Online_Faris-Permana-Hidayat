
document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* =========================================
           NAVBAR
        ========================================= */

        const navbar =
    document.getElementById("navbar");

if (navbar) {

    window.addEventListener(
        "scroll",
        function () {

            if (window.scrollY > 30) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }

        }
    );

}

        /* =========================================
           MOBILE MENU
        ========================================= */

       const menuToggle =
    document.getElementById("menuToggle");

const mobileMenu =
    document.getElementById("mobileMenu");

if (menuToggle && mobileMenu) {

    menuToggle.addEventListener(
        "click",
        function () {

            mobileMenu.classList.toggle("active");

        }
    );

    const mobileLinks =
        mobileMenu.querySelectorAll("a");

    mobileLinks.forEach(
        function (link) {

            link.addEventListener(
                "click",
                function () {

                    mobileMenu.classList.remove(
                        "active"
                    );

                }
            );

        }
    );

}
        /* =========================================
           SCROLL REVEAL
        ========================================= */

        const revealElements =
            document.querySelectorAll(".reveal");


        const observer =
            new IntersectionObserver(
                function (entries) {

                    entries.forEach(
                        function (entry) {

                            if (
                                entry.isIntersecting
                            ) {

                                entry.target.classList.add(
                                    "visible"
                                );

                                observer.unobserve(
                                    entry.target
                                );

                            }

                        }
                    );

                },
                {
                    threshold: 0.12
                }
            );


        revealElements.forEach(
            function (element) {

                observer.observe(element);

            }
        );


        /* =========================================
           COUNTDOWN
        ========================================= */

        function updateCountdown() {

            const countdowns =
                document.querySelectorAll(
                    ".deadline"
                );


            countdowns.forEach(
                function (element) {

                    const deadlineText =
                        element.dataset.deadline;


                    if (!deadlineText) {

                        element.innerHTML =
                            "⚠️ Deadline belum tersedia";

                        return;

                    }


                    const deadline =
                        new Date(
                            deadlineText.replace(
                                " ",
                                "T"
                            )
                        ).getTime();


                    const now =
                        new Date().getTime();


                    if (isNaN(deadline)) {

                        element.innerHTML =
                            "⚠️ Format deadline tidak valid";

                        return;

                    }


                    const distance =
                        deadline - now;


                    if (distance <= 0) {

                        element.innerHTML =
                            "🔒 Campaign telah ditutup";

                        element.style.background =
                            "#111";

                        element.style.color =
                            "#777";

                        return;

                    }


                    const days =
                        Math.floor(
                            distance /
                            (1000 * 60 * 60 * 24)
                        );


                    const hours =
                        Math.floor(
                            (
                                distance %
                                (1000 * 60 * 60 * 24)
                            ) /
                            (1000 * 60 * 60)
                        );


                    const minutes =
                        Math.floor(
                            (
                                distance %
                                (1000 * 60 * 60)
                            ) /
                            (1000 * 60)
                        );


                    const seconds =
                        Math.floor(
                            (
                                distance %
                                (1000 * 60)
                            ) /
                            1000
                        );


                    if (days > 0) {

                        element.innerHTML =
                            "⏳ " +
                            days +
                            " hari lagi sebelum ditutup";

                    } else {

                        element.innerHTML =
                            "⚡ " +
                            hours +
                            "j " +
                            minutes +
                            "m " +
                            seconds +
                            "d lagi";

                    }

                }
            );

        }


        updateCountdown();

        setInterval(
            updateCountdown,
            1000
        );


    }
);

