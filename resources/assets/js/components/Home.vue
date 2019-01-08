<template>
    <div class="flex flex-col h-screen justify-center items-center" >
        <p class="text-grey mb-4 text-5xl font-hairline m-0">Mothership</p>
        <div class="border border-grey-light w-64 max-w-full my-4 mb-8"></div>
        <p class="text-grey">Nuber of Listings: {{ numListings.toLocaleString() }}</p>
        <p class="text-grey">Number of Agents: {{ numAgents.toLocaleString() }}</p>
        <p class="weight-hairline text-grey mt-8 text-3xl">{{ now }}</p>
    </div>
</template>

<script>
export default {
    props: [
        'now', 'numListings', 'numAgents'
    ]
}
</script>

