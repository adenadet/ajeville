<template>
    <section class="overlay-wrapper">
        <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="direct-chat-messages">
            <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                    <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                </div>
                <img class="direct-chat-img" :src="'dist/img/user1-128x128.jpg'" alt="message user image">
                <div class="direct-chat-text">
                    Is this template really for free? That's unbelievable!
                </div>
            </div>
            <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                    <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                </div>
                <img class="direct-chat-img" :src="'dist/img/user3-128x128.jpg'" alt="message user image">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                    You better believe it!
                </div>
            </div>
            <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                    <span class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                </div>
                <img class="direct-chat-img" :src="'dist/img/user1-128x128.jpg'" alt="message user image">
                <div class="direct-chat-text">
                    Working with AdminLTE on a great new app! Wanna join?
                </div>
            </div>
            <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                    <span class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                </div>
                <img class="direct-chat-img" :src="'dist/img/user3-128x128.jpg'" alt="message user image">
                <div class="direct-chat-text">
                    I would love to.
                </div>
            </div>
        </div>
    </section>
</template>