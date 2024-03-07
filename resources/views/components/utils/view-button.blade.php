@props(['href' => '#', 'permission' => false])

<x-utils.link :href="$href" class="btn btn-info btn-sm" :text="__('View')" permission="{{ $permission }}" />
