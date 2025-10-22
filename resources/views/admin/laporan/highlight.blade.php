<div class="d-flex align-items-center justify-content-between">
                  @if($laporanDiproses->count() > 0)
                      <button id="prev-card" class="btn btn-light btn-sm"><i class="fas fa-chevron-left"></i></button>
                  @endif
                  <div class="flex-grow-1 px-3">
                      <div id="laporan-highlight-card" class="position-relative" style="background: #e1dfec;">
                          @forelse($laporanDiproses as $i => $laporan)
                              <div class="laporan-highlight-card p-3 border rounded shadow-sm
                                          {{ $i === 0 ? '' : 'd-none' }}"
                                    id="laporan-card-{{ $laporan->id }}"
                                  data-id="{{ $laporan->id }}"
                                  data-topic="{{ $laporan->topic }}"
                                  data-sender="{{ $laporan->user?->name ?? 'Anonim' }}"
                                  data-created_at="{{ $laporan->created_at_tz }}"
                                  data-date="{{ $laporan->date_tz }}"
                                  data-place="{{ $laporan->place }}"
                                  data-chronology="{{ $laporan->chronology }}"
                                  data-status="{{ $laporan->status }}">
                                  <div class="d-flex justify-content-between align-items-center mb-1">
                                      <h3 class="mb-0">{{ $laporan->topic }}</h3>
                                      <span class="badge bg-{{ $laporan->status === 'Menunggu' ? 'warning' : 'primary' }}">
                                          {{ ucfirst($laporan->status) }}
                                      </span>
                                  </div>
                                  <small class="text-muted">
                                    {{ $laporan->user?->name ?? 'Anonim' }} · {{ $laporan->created_at_tz }}
                                </small>
                                  <p class="mt-2 mb-0 text-truncate">Tempat: {{$laporan->place}}</p>
                                  <p class="mt-2 mb-0 text-truncate">Tanggal: {{ $laporan->date_tz }}</p>
                                  <p class="mt-2 mb-0 text-truncate">Kronologis: {{ Str::limit($laporan->chronology, 120) }}</p>
                              </div>
                         @empty
                              <div id="no-laporan" class="p-5 text-center text-muted border rounded">
                                  <i class="fas fa-inbox fa-2x mb-3"></i>
                                  <p class="mb-0">Tidak ada laporan menunggu atau diproses</p>
                              </div>
                          @endforelse
                      </div>
                  </div>

                  @if($laporanDiproses->count() > 0)
                  <button id="next-card" class="btn btn-light btn-sm"><i class="fas fa-chevron-right"></i></button>
                  @endif
                </div>

                <!-- Carousel Indicators -->
                 @if(count($laporanDiproses) > 1)
                     <div id="laporan-indicators" class="d-flex justify-content-center mt-2"></div>
                 @endif
             </div>
