@props(['href' => '#', 'permission' => false])

<x-utils.link :href="$href" class="btn btn-primary btn-sm" :text="__('Edit')" permission="{{ $permission }}" />
