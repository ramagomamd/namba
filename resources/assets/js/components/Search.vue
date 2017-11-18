<template>
	<div class="col-md-12" style="margin-bottom: 2em">
	  <ais-index :search-store="searchStore">
	    <ais-search-box>
	        <div class="input-group" style="margin-bottom: 1em">
	          <ais-input placeholder="Search tracks by title or albums..."
	            :class-names="{'ais-input': 'form-control'}">
	          </ais-input>

	          <span class="input-group-btn">
	            <ais-clear :class-names="{'ais-clear': 'btn btn-default'}">
	              <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
	            </ais-clear>
	            <button class="btn btn-default" type="submit">
	              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
	            </button>
	          </span>
	        </div><!-- /input-group -->
	    </ais-search-box>

	    <ais-results v-show="searchStore.query.length > 0">
	       <template scope="{ result }">
	           <div class="list-group">
					<ul class="list-group">
						<li class="list-group-item">
							<a :href="result.route" class="text-success">
								<strong>{{ result.full_title }}</strong>
							</a>
						</li>
						<li class="list-group-item">{{ result.album }}</li>
					</ul>
	           </div>
	       </template>
	    </ais-results>

	    <ais-no-results></ais-no-results>
		<div v-show="searchStore.query.length > 0">
			<ais-pagination :padding="5" v-on:page-change="onPageChange" 
		        :class-names="{
		          'ais-pagination': 'pagination',
		          'ais-pagination__item': 'page',
		          'ais-pagination__item--active': 'active',
		        }">
		    </ais-pagination>
		    <ais-powered-by class="pull-right" style="padding-top: 1em"></ais-powered-by>
		</div>

	  </ais-index>
	</div>
</template>

<script>
import { createFromAlgoliaCredentials } from 'vue-instantsearch';

const searchStore = createFromAlgoliaCredentials('0NHS3UUMUF', '34c27b0fe78e169deec6704f186698fe');
searchStore.indexName = 'tracks'

export default {
  data() {
  	return {
  		searchStore
  	}
  },
  methods: {
	  onPageChange() {
	    window.scrollTo(0,0);
	  }
	}
}
</script>